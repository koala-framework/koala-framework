<?php
abstract class Vps_Component_Renderer_Abstract
{
    private $_ignoreVisible = false;
    private $_enableCache = false;
    private $_plugins = array();
    private $_renderComponentId;
    private $_stats = array();

    private $_cacheModel;
    private $_cache = array();

    protected function _saveCache(Vps_Component_Data $component, $type, $value, $content)
    {
        $settings = $component->getComponent()->getViewCacheSettings();
        if (!$settings['enabled']) return false;

        $page = $component;
        while ($page && !$page->isPage) $page = $page->parent;
        $expire = is_null($settings['lifetime']) ? 0 : time() + $settings['lifetime'];

        $model = $this->_getCacheModel();
        $data = array(
            'component_id' => $component->componentId,
            'page_id' => $page ? $page->componentId : null,
            'component_class' => $component->componentClass,
            'type' => $type,
            'value' => $value,
            'expire' => $expire,
            'deleted' => 0,
            'content' => $content
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_getCacheModel()->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
        return true;
    }

    protected function _loadCache($component)
    {
        $ret = array();
        $select = $this->_getCacheModel()->select();
        $or = array();
        while ($component && !$component->isPage) $component = $component->parent;
        if ($component) {
            $or[] = new Vps_Model_Select_Expr_Equals('page_id', $component->componentId);
        } else {
            $or[] = new Vps_Model_Select_Expr_IsNull('page_id');
        }
        while ($component) {
            $component = $component->parent;
            if ($component && $component->isPage) {
                $or[] = new Vps_Model_Select_Expr_And(array(
                    new Vps_Model_Select_Expr_Equals('page_id', $component->componentId),
                    new Vps_Model_Select_Expr_Equals('type', 'box')
                ));
            }
        }
        $select->where(new Vps_Model_Select_Expr_Or($or));
        foreach ($this->_getCacheModel()->export(Vps_Model_Db::FORMAT_ARRAY, $select) as $row) {
            if ($row['expire'] == 0 || $row['expire'] > time()) {
                $ret[$row['type']][(string)$row['component_id']][$row['value']] = $row['content'];
            }
        }
        return $ret;
    }

    private function _getCacheModel()
    {
        if (!$this->_cacheModel) {
            $this->_cacheModel = Vps_Model_Abstract::getInstance('Vps_Component_Renderer_CacheModel');
        }
        return $this->_cacheModel;
    }

    public function setCacheModel(Vps_Component_Renderer_CacheModel $model)
    {
        $this->_cacheModel = $model;
    }

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
        if ($this->_renderComponentId == $componentId &&
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
        $this->_renderComponentId = $component->componentId;
        $this->_stats = array(
            'rendered' => array(),
            'cacheSaved' => array(),
            'cachePreloaded' => array(),
            'cacheLoaded' => array()
        );
        if ($this->_enableCache) {
            $this->_cache = $this->_loadCache($component);
            $this->_stats['cachePreloaded'] = $this->_cache;
        }
        $ret = Vps_Component_Output_Component::getHelperOutput($component);
        return $this->render($ret);
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

        while (preg_match('/{([^ }]+): ([^ }]+)(\([^ }]+\) )?([^}]*)}/', $ret, $matches)) {
            $type = $matches[1];
            $componentId = trim($matches[2]);
            $value = trim($matches[3]);
            if ($value == '') $value = null;
            $config = trim($matches[4]);
            $config = $config != '' ? explode(' ', trim($config)) : array();

            $component = $this->_getComponent($componentId);

            if (!$component) {
                throw new Vps_Exception("Could not find component with id $componentId for rendering.");
            }

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
                $stats['cacheLoaded'][] = $componentId;

            } else {

                $class = 'Vps_Component_Output_' . ucfirst($type);
                $output = new $class();
                $view->clearVars();
                $content = $output->render($component, $config, $view);
                $stats['rendered'][] = $componentId;
                foreach ($plugins as $plugin) {
                    if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_BEFORE) {
                        $content = $plugin->processOutput($content);
                    } else if ($plugin->getExecutionPoint() == Vps_Component_Plugin_Interface_View::EXECUTE_AFTER) {
                        $pluginNr++;
                        $pluginClass = get_class($plugin);
                        $content = "{plugin $pluginNr $pluginClass $componentId}$content{/plugin $pluginNr}";
                    }
                }

                if ($this->_enableCache) {
                    $written = $this->_saveCache(
                        $component,
                        $type,
                        $output->getCacheValue(),
                        $content
                    );
                    if ($written) $stats['cacheSaved'][] = $componentId;
                }

            }
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
