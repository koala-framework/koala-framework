<?php
abstract class Vps_Component_Renderer_Abstract
{
    private $_enableCache = false;
    private $_renderComponent;
    private $_cache = array();
    private $_cacheUsed = array();
    private $_preloaded = array();

    public function setEnableCache($enableCache)
    {
        $this->_enableCache = true;
    }

    public function renderComponent($component)
    {
        $this->_renderComponent = $component;
        $this->_stats = array(
            'rendered' => array(),
            'cacheSaved' => array(),
            'cachePreloaded' => array(),
            'cacheRendered' => array()
        );
        if ($this->_enableCache) {
            $page = $component;
            while ($page && !$page->isPage) $page = $page->parent;
            $pageId = $page ? $page->componentId . '%' : null; // null nur bei tests wenn root ein template hat
            $this->_preload(array($pageId));
        }
        $view = new Vps_Component_View($this);
        $ret = $this->render($view->component($component));
        if ($this->_enableCache) {
            Vps_Component_Cache::getInstance()->writeBuffer();
        }
        return $ret;
    }

    private function _preload($componentIds)
    {
        Vps_Benchmark::count("cache preload", implode(', ', $componentIds));

        $where = array();
        foreach ($componentIds as $componentId) {
            $pageId = $this->_getPageIdFromComponentId($componentId);

            if (!isset($where[$pageId])) $where[$pageId] = array();
            $where[$pageId][] = $componentId;

            if (!isset($this->_preloaded[$pageId])) $this->_preloaded[$pageId] = array();
            $this->_preloaded[$pageId][] = $componentId;
            $this->_preloaded[$pageId] = array_unique($this->_preloaded[$pageId]);
        }

        $c = Vps_Component_Cache::getInstance()->preload($where);
        foreach ($c as $type => $componentIds) {
            foreach ($componentIds as $componentId => $values) {
                foreach ($values as $value => $content) {
                    $this->_cache[$type][$componentId][$value] = $content;
                }
            }
        }
    }

    private function _getPageIdFromComponentId($componentId)
    {
        $pageId = $componentId;
        $pos = strpos($pageId, '%');
        if ($pos !== false) {
            $pageId = substr($pageId, 0, $pos);
        }
        $pos = strpos($pageId, '_');
        if ($pos !== false) {
            $pageId = substr($pageId, 0, $pos);
        }
        return $pageId;
    }

    private function _isPreloaded($componentId)
    {
        $pageId = $this->_getPageIdFromComponentId($componentId);
        foreach ($this->_preloaded as $preloadedPageId => $preloadedComponentIds) {
            foreach ($preloadedComponentIds as $preloadedComponentId) {
                if ($preloadedPageId == $pageId) {
                    $pattern = '/^' . str_replace('%', '[0-9\-_]*', $preloadedComponentId) . '/';
                    preg_match($pattern, $componentId, $matches);
                    if ($matches) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function render($ret = null)
    {
        $pluginNr = 0;
        $render = true;
        $pregType = 'cc';
        $toPreload = array();

        $helpers = array();

        /*
        So lange rendern bis nichts mehr zu rendern ist (rendered==0)
        Falls nicht in Cache regulären Ausdruck ersetzen ({cc ...} -> {pc ...})
        und so lange cc weiterrendern bis nichts mehr neues kommt
        Danach alle pc laden und pc rendern
        Danach wieder auf cc umschalten usw.
        */
        while ($render) {

            $rendered = 0;

            // {cc type: componentId(value)[plugins] config}
            while (preg_match('/{' . $pregType . ' ([a-z]+): ([^ \[}\(]+)(\([^ }]+\))?(\[[^}]+\])?( [^}]*)}/i', $ret, $matches)) {
                $rendered++;
                $type = $matches[1];
                $componentId = trim($matches[2]);
                $value = (string)trim($matches[3]); // Bei Partial partialId oder bei master component_id zu der das master gehört
                if ($value) $value = substr($value, 1, -1);
                $plugins = trim($matches[4]);
                if ($plugins) $plugins = explode(' ', substr($plugins, 1, -1));
                if (!$plugins) $plugins = array();
                $config = trim($matches[5]);
                $config = $config != '' ? unserialize(base64_decode($config)) : array();

                $statId = $componentId;
                if ($value) $statId .= " ($value)";
                if ($type != 'component') $statId .= ': ' . $type;

                if (!isset($helpers[$type])) {
                    $class = 'Vps_Component_View_Helper_' . ucfirst($type);
                    $helper = new $class();
                    $helper->setRenderer($this);
                    $helpers[$type] = $helper;
                } else {
                    $helper = $helpers[$type];
                }
                $statType = null;

                if ($this->_enableCache && isset($this->_cache[$type][$componentId][$value])) {

                    if (Vps_Benchmark::isEnabled()) {
                        $this->_cacheUsed[] = array($type, $componentId, $value);
                    }
                    $content = $this->_cache[$type][$componentId][$value];
                    $content = $helper->renderCached($content, $componentId, $config);
                    $statType = 'cache';
                    $processPlugins = true;

                } else if ($this->_enableCache && !$this->_isPreloaded($componentId)) {

                    $toPreload[] = $componentId . '%';
                    $content = str_replace('{cc ', '{pc ', $matches[0]);
                    $processPlugins = false;

                } else {

                    $content = $helper->render($componentId, $config);

                    if ($this->_enableCache && $helper->saveCache($componentId, $config, $value, $content)) {
                        $statType = 'nocache';
                    } else {
                        $statType = 'noviewcache';
                    }
                    $content = $helper->renderCached($content, $componentId, $config);
                    $processPlugins = true;

                }

                if ($processPlugins) {
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
                }

                if ($statType) Vps_Benchmark::count("rendered $statType", $statId);
                $ret = str_replace($matches[0], $content, $ret);
            }

            if ($rendered == 0 && count($toPreload) > 0) {
                $this->_preload($toPreload);
                $toPreload = array();
                $pregType = 'pc';
            } else if ($pregType == 'pc') {
                $pregType = 'cc';
            } else if ($rendered == 0) {
                $render = false;
            }

        }

        while (preg_match('/{plugin (\d) ([^}]*) ([^}]*)}(.*){\/plugin \\1}/', $ret, $matches)) {
            $pluginClass = $matches[2];
            $plugin = new $pluginClass($matches[3]);
            $content = $plugin->processOutput($matches[4]);
            $ret = str_replace($matches[0], $content, $ret);
        }

        if (Vps_Benchmark::isEnabled()) {
            foreach ($this->_cache as $type=>$i1) {
                foreach ($i1 as $componentId => $i2) {
                    foreach ($i2 as $value => $content) {
                        if (!in_array(array($type, $componentId, $value), $this->_cacheUsed)) {
                            Vps_Benchmark::count('preloaded but unused', $type.' '.$componentId.' '.$value);
                        }
                    }
                }
            }
        }

        return $ret;
    }

    public function getRenderComponent()
    {
        return $this->_renderComponent;
    }
}
