<?php
abstract class Vps_Component_Renderer_Abstract
{
    private $_ignoreVisible = false;
    private $_enableCache = false;
    private $_plugins = array();
    private $_stats = array();
    protected $_renderComponent;

    public function setIgnoreVisible($ignoreVisible)
    {
        $this->_ignoreVisible = $ignoreVisible;
    }

    public function ignoreVisible()
    {
        return $this->_ignoreVisible;
    }

    public function setEnableCache($enableCache)
    {
        $this->_enableCache = $enableCache;
    }

    protected function _getComponent($componentId)
    {
        $ret = Vps_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => $this->ignoreVisible()));
        if (!$ret) throw new Vps_Exception("Can't find component '$componentId' for rendering");
        return $ret;
    }

    protected function _getPlugins($component)
    {
        $ret = array();
        $componentId = $component->componentId;

        // Keine Plugins bei Startkomponente außer es ist die root
        if ($this->_renderComponent->componentId == $componentId &&
            $componentId != Vps_Component_Data_Root::getInstance()->componentId
        ) return $ret;

        if (!isset($this->_plugins[$componentId])) {
            $this->_plugins[$componentId] = array();
            foreach ($component->getPlugins() as $pluginClass) {
                $plugin = new $pluginClass($componentId);
                if (!$plugin instanceof Vps_Component_Plugin_Abstract)
                    throw Vps_Exception('Plugin must be Instanceof Vps_Component_Plugin_Abstract');
                $this->_plugins[$componentId][] = $plugin;
            }
        }

        if (count($this->_plugins[$componentId])) {
            $ret = $this->_plugins[$componentId];
            $this->_plugins[$componentId] = array();
        }
        return $ret;
    }

    public function renderComponent($component)
    {
        $this->_plugins = array();
        $this->_renderComponent = $component;
        $this->_stats = array(
            'rendered' => array(),
            'cacheSaved' => array(),
            'cachePreloaded' => array(),
            'cacheLoaded' => array()
        );
        if ($this->_enableCache) {
            $this->_cache = Vps_Component_Cache::getInstance()->load($component);
            foreach ($this->_cache as $type => $componentIds) {
                foreach ($componentIds as $componentId => $values) {
                    foreach ($values as $value => $null) {
                        $statId = $componentId;
                        if ($value) $statId .= ' (' . $value .')';
                        if ($type != 'component') $statId .= ': ' . $type;
                        $this->_stats['cachePreloaded'][] = $statId;
                    }
                }
            }
        }
        $ret = Vps_Component_Output_Component::getHelperOutput($component);
        $ret = $this->render($ret);
        return $ret;
    }

    protected function _formatOutputConfig($outputConfig, $component)
    {
        return $outputConfig;
    }

    public function render($ret)
    {
        if ($ret instanceof Vps_Component_Data) return $this->renderComponent($ret);

        $view = $this->_getView();
        $pluginNr = 0;
        $stats = $this->_stats;

        while (preg_match('/{([^ }]+): ([^ }\(]+)(\([^ }]+\) )?([^}]*)}/', $ret, $matches)) {
            $type = $matches[1];
            $componentId = trim($matches[2]);
            $value = (string)trim($matches[3]);
            if ($value) $value = substr($value, 1, -1);
            $config = trim($matches[4]);
            $config = $config != '' ? explode(' ', trim($config)) : array();


            $statId = $componentId;
            if ($value) $statId .= " ($value)";
            if ($type != 'component') $statId .= ': ' . $type;

            $component = $this->_getComponent($componentId);
            $outputConfig = array(
                'type' => $type,
                'value' => $value,
                'config' => $config,
                'plugins' => array()
            );
            $outputConfig = $this->_formatOutputConfig($outputConfig, $component);
            $type = $outputConfig['type'];
            $config = $outputConfig['config'];
            $plugins = $outputConfig['plugins'];
            $value = $outputConfig['value'];

            if ($this->_enableCache && isset($this->_cache[$type][$componentId][$value])) {

                $content = $this->_cache[$type][$componentId][$value];
                $stats['cacheRendered'][] = $statId;
                $statType = 'cache';

            } else {

                $class = 'Vps_Component_Output_' . ucfirst($type);
                $output = new $class();
                $view->clearVars();
                $content = $output->render($component, $config, $view);
                $stats['rendered'][] = $statId;
                foreach ($plugins as $plugin) {
                    if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_BEFORE) {
                        $content = $plugin->processOutput($content);
                    } else if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_AFTER) {
                        $pluginNr++;
                        $pluginClass = get_class($plugin);
                        $content = "{plugin $pluginNr $pluginClass $componentId}$content{/plugin $pluginNr}";
                    }
                }

                $cacheSettings = $component->getComponent()->getViewCacheSettings();
                $statType = $this->_enableCache && $cacheSettings['enabled'] ?
                    'nocache' : 'noviewcache';

                if ($this->_enableCache && $cacheSettings['enabled']) {
                    $written = Vps_Component_Cache::getInstance()->save(
                        $component,
                        $content,
                        $type,
                        $output->getCacheValue()
                    );
                    if ($written) $stats['cacheSaved'][] = $statId;
                    $page = $component->getPage();
                    $cPage = $this->_renderComponent->getPage();
                    if ($page && $cPage && $page->componentId != $cPage->componentId) {
                        Vps_Component_Cache::getInstance()->savePreload($this->_renderComponent, $component);
                    }
                }

            }

            Vps_Benchmark::count("rendered $statType", $statId);
            $ret = str_replace($matches[0], $content, $ret);
        }

        while (preg_match('/{plugin (\d) ([^}]*) ([^}]*)}(.*){\/plugin \\1}/', $ret, $matches)) {
            $pluginClass = $matches[2];
            $plugin = new $pluginClass($matches[3]);
            $content = $plugin->processOutput($matches[4]);
            $ret = str_replace($matches[0], $content, $ret);
        }

        $this->_stats = $stats;
        return $ret;
    }

    public function getStats()
    {
        return $this->_stats;
    }

    protected function _getView()
    {
        return new Vps_View();
    }
}
