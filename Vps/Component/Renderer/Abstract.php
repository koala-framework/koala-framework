<?php
abstract class Vps_Component_Renderer_Abstract
{
    private $_ignoreVisible = false;
    private $_enableCache = false;
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

    public function renderComponent($component)
    {
        $this->_plugins = array();
        $this->_renderComponent = $component;
        $this->_stats = array(
            'rendered' => array(),
            'cacheSaved' => array(),
            'cachePreloaded' => array(),
            'cacheRendered' => array()
        );
        if ($this->_enableCache) {
            $this->_cache = Vps_Component_Cache::getInstance()->preload($component);
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
        $view = $this->_getView();
        $ret = $this->render($view, $view->component($component));
        return $ret;
    }

    protected function _getOutputConfig($type)
    {
        return array();
    }

    public function render($view, $ret = null)
    {
        if ($view instanceof Vps_Component_Data) return $this->renderComponent($view);
        if (!$view instanceof Vps_View) throw new Vps_Exception('Need view for rendering');

        $pluginNr = 0;
        $stats = $this->_stats;

        while (preg_match('/{(!)?([^ }]+): ([^ \[}\(]+)(\([^ }]+\))?(\[[^}]+\])?( [^}]*)}/', $ret, $matches)) {
            $useCache = $matches[1] != '!';
            $type = $matches[2];
            $componentId = trim($matches[3]);
            $value = (string)trim($matches[4]);
            if ($value) $value = substr($value, 1, -1);
            $plugins = trim($matches[5]);
            if ($plugins) $plugins = explode(' ', substr($plugins, 1, -1));
            if (!$plugins) $plugins = array();
            $config = trim($matches[6]);
            $config = $config != '' ? explode(' ', trim($config)) : array();

            foreach ($this->_getOutputConfig($type) as $key => $val) {
                $$key = $val;
            }

            $statId = $componentId;
            if ($value) $statId .= " ($value)";
            if ($type != 'component') $statId .= ': ' . $type;

            if ($this->_enableCache && isset($this->_cache[$type][$componentId][$value])) {

                $content = $this->_cache[$type][$componentId][$value];
                $stats['cacheRendered'][] = $statId;
                $statType = 'cache';

            } else {

                $class = 'Vps_Component_View_Helper_' . ucfirst($type);
                $helper = new $class();
                $view->clearVars();
                if ($useCache) {
                    $component = $this->_getComponent($componentId);
                } else {
                    $component = $componentId;
                }
                $content = $helper->render($component, $config, $view);
                $stats['rendered'][] = $statId;

                if ($this->_enableCache) {
                    if ($useCache) {
                        $cacheSettings = $component->getComponent()->getViewCacheSettings();
                        if (!$cacheSettings['enabled']) $useCache = false;
                    }
                    if ($useCache) {
                        $written = $helper->saveCache($component, $content);
                        if ($written) {
                            $stats['cacheSaved'][] = $statId;
                            $page = $component->getPage();
                            $cPage = $this->_renderComponent->getPage();
                            if ($page && $cPage && $page->componentId != $cPage->componentId) {
                                Vps_Component_Cache::getInstance()->savePreload($this->_renderComponent, $component);
                            }
                        }
                    }
                }

                $statType = $useCache ? 'nocache' : 'noviewcache';
            }

            foreach ($plugins as $pluginClass) {
                $plugin = new $pluginClass($componentId);
                if (!$plugin instanceof Vps_Component_Plugin_Abstract)
                    throw Vps_Exception('Plugin must be Instanceof Vps_Component_Plugin_Abstract');
                if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_BEFORE) {
                    $content = $plugin->processOutput($content);
                } else if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_AFTER) {
                    $pluginNr++;
                    $content = "{plugin $pluginNr $pluginClass $componentId}$content{/plugin $pluginNr}";
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
        $ret = new Vps_Component_View();
        $ret->setRenderComponent($this->_renderComponent);
        return $ret;
    }
}
