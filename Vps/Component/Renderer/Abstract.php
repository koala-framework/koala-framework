<?php
abstract class Vps_Component_Renderer_Abstract
{
    private $_enableCache = false;
    private $_stats = array();
    private $_renderComponent;
    private $_cache = array();

    public function setEnableCache($enableCache)
    {
        $this->_enableCache = $enableCache;
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
        $this->_cache = array();
        if ($this->_enableCache) {
            $page = $component;
            while ($page && !$page->isPage) $page = $page->parent;
            if ($page) {
                $where = array('page_id' => $page->componentId);
            } else {
                $where = array('page_id' => null);
            }
            $this->_preload($where);
        }
        $view = new Vps_Component_View($this);
        $ret = $this->render($view->component($component));
        if ($this->_enableCache) {
            Vps_Component_Cache::getInstance()->writeBuffer();
        }
        return $ret;
    }

    private function _preload($where)
    {
        $c = Vps_Component_Cache::getInstance()->preload($where);
        foreach ($c as $type => $componentIds) {
            foreach ($componentIds as $componentId => $values) {
                foreach ($values as $value => $content) {
                    $this->_cache[$type][$componentId][$value] = $content;
                }
            }
        }

        $this->_stats['cachePreloaded'] = array();
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

    /**
     * Eigentliche Render-Schleife
     *
     * Parst in einer Schleife $ret und rendert die gleiche View immer wieder.
     * In der View können Daten über mehrere Render- und Helper-Aufrufe hinweg gespeichert
     * werden, deshalb immer die gleiche View.
     *
     * @param $view
     * @param $ret
     */
    public function render($ret = null)
    {
        $pluginNr = 0;
        $preloaded = array();
        $stats = $this->_stats;

        $helpers = array();

        // {type: componentId(value)[plugins] config}
        $x=0;
        while ($x<100 && preg_match_all('/{cc ([a-z]+): ([^ \[}\(]+)(\([^ }]+\))?(\[[^}]+\])?( [^}]*)}/i', $ret, $matches)) {
            $x++;
            $toPreload = array();
            for ($key = 0; $key < count($matches[0]); $key++) {
                $type = $matches[1][$key];
                $componentId = trim($matches[2][$key]);
                $value = (string)trim($matches[3][$key]); // Bei Partial partialId oder bei master component_id zu der das master gehört
                if ($value) $value = substr($value, 1, -1);
                $plugins = trim($matches[4][$key]);
                if ($plugins) $plugins = explode(' ', substr($plugins, 1, -1));
                if (!$plugins) $plugins = array();
                $config = trim($matches[5][$key]);
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

                if ($this->_enableCache) {
                    $cacheSet =
                        array_key_exists($type, $this->_cache) &&
                        array_key_exists($componentId, $this->_cache[$type]) &&
                        array_key_exists($value, $this->_cache[$type][$componentId]);
                    $content = $cacheSet ? $this->_cache[$type][$componentId][$value] : null;
                }

                if ($this->_enableCache && $cacheSet && !is_null($content)) {

                    $content = $helper->renderCached($content, $componentId, $config);
                    $stats['cacheRendered'][] = $statId;
                    $statType = 'cache';

                } else if ($this->_enableCache && !$cacheSet && !in_array($componentId, $preloaded)) {

                    $toPreload[] = $componentId;

                } else {

                    $content = $helper->render($componentId, $config);
                    $stats['rendered'][] = $statId;

                    if ($this->_enableCache && $helper->saveCache($componentId, $config, $value, $content)) {
                        $stats['cacheSaved'][] = $statId;
                        $statType = 'nocache';
                    } else {
                        $statType = 'noviewcache';
                    }
                    $content = $helper->renderCached($content, $componentId, $config);

                }

                if (!is_null($content)) {
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
                    $ret = str_replace($matches[0][$key], $content, $ret);
                }
            }

            if ($toPreload) {
                //p($toPreload);
                $this->_preload(array('component_id' => $toPreload));
                $preloaded = array_merge($preloaded, $toPreload);
            }

        }

        while (preg_match('/{plugin (\d) ([^}]*) ([^}]*)}(.*){\/plugin \\1}/', $ret, $matches)) {
            $pluginClass = $matches[2];
            $plugin = new $pluginClass($matches[3]);
            $content = $plugin->processOutput($matches[4]);
            $ret = str_replace($matches[0], $content, $ret);
        }
        $this->_stats = $stats;
        //p($this->_stats);
        return $ret;
    }

    public function getStats()
    {
        return $this->_stats;
    }

    public function getRenderComponent()
    {
        return $this->_renderComponent;
    }
}
