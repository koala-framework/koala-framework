<?php
abstract class Kwf_Component_Renderer_Abstract
{
    protected $_enableCache = null;
    protected $_renderComponent;

    protected $_includedComponents = array();

    public function includedComponent($targetComponentId, $targetType)
    {
        $this->_includedComponents[] = array(
            'target_id' => $targetComponentId,
            'target_type' => $targetType
        );
    }

    public function __construct()
    {
        $this->_enableCache = !Kwf_Config::getValue('debug.componentCache.disable');
        if (Kwf_Component_Data_Root::getShowInvisible()) {
            $this->_enableCache = false;
        }
    }

    public function setEnableCache($enableCache)
    {
        if ($enableCache === null) throw new Kwf_Exception('expected boolean parameter');
        $this->_enableCache = $enableCache;
    }

    public function renderComponent($component)
    {
        $this->_renderComponent = $component;
        $content = $this->_renderComponentContent($component);
        $ret = $this->_render(2, $content);
        Kwf_Component_Cache::getInstance()->writeBuffer();
        return $ret;
    }

    protected abstract function _getCacheName();

    public function getTemplate(Kwf_Component_Data $component, $type)
    {
        $template = Kwc_Abstract::getTemplateFile($component->componentClass, $type);
        if (!$template) throw new Kwf_Exception("No $type-Template found for '{$component->componentClass}'");
        return $template;
    }

    protected function _renderComponentContent($component)
    {
        $masterHelper = new Kwf_Component_View_Helper_Component();
        $masterHelper->setRenderer($this);
        return $masterHelper->component($component);
    }

    //TODO: where is this used?
    public function render($ret)
    {
        $ret = $this->_render(2, $ret);
        return $ret;
    }

    private function _executePlugins($ret, $pluginType)
    {
        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        while (($start = strpos($ret, '<plugin'.$pluginType.' ')) !== false) {
            $startEnd = strpos($ret, '>', $start);
            $args = explode(' ', substr($ret, $start+9, $startEnd-$start-9));
            $end = strpos($ret, '</plugin'.$pluginType.' '.$args[0].'>');
            $content = substr($ret, $startEnd+1, $end-$startEnd-1);
            if ($benchmarkEnabled) $startTime = microtime(true);
            $plugin = Kwf_Component_Plugin_Abstract::getInstance($args[1], $args[2]);
            $content = $plugin->processOutput($content);
            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint('plugin '.$args[1], microtime(true)-$startTime);
            $ret = substr($ret, 0, $start).$content.substr($ret, $end+11+strlen($args[0]));
        }
        return $ret;
    }

    /**
     * Render components (ie. expand <cc ...>)
     *
     * @param int render pass; 1 or 2: 1 for content that can be stored in fullPage cache,
                                       2 for everything else. 2 includes 1, so calling just with 2 also works
     * @param string render content
     */
    protected function _render($pass, $ret)
    {
        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        $pluginNr = 0;
        $helpers = array();

        if ($pass == 2) {
            //in second pass execute all EXECUTE_BEFORE plugins
            $ret = $this->_executePlugins($ret, 'B');
        }

        $offset = 0;
        while (($start = strpos($ret, '<kwc', $offset)) !== false) {
            $p = substr($ret, $start+4, 1);
            if ($pass == 1 && $p == 2) {
                $offset = $start+1;
                continue; //matches $pass? (first char after {cc)
            }
            if ($benchmarkEnabled) $startTime = microtime(true);
            $end = strpos($ret, '>', $start);

            $args = explode(' ', substr($ret, $start+6, $end-$start-6));
            $type = $args[0];
            $componentId = $args[1];
            $value = (string)$args[2]; // Bei Partial partialId oder bei master component_id zu der das master gehört
            $plugins = json_decode($args[3], true);
            $config = $args[4];
            $config = $config != '' ? unserialize(base64_decode($config)) : array();

            if (isset($plugins['replace'])) {
                foreach ($plugins['replace'] as $pluginClass) {
                    $plugin = Kwf_Component_Plugin_Abstract::getInstance($pluginClass, $componentId);
                    $content = $plugin->replaceOutput();
                    if ($content !== false) {
                        if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($componentId.' plugin', microtime(true)-$startTime);
                        $ret = substr($ret, 0, $start).$content.substr($ret, $end+1);
                        continue 2;
                    }
                }
            }

            $statId = $componentId;
            if ($value) $statId .= " ($value)";
            if ($type != 'component') $statId .= ': ' . $type;

            if (!isset($helpers[$type])) {
                $class = 'Kwf_Component_View_Helper_' . ucfirst($type);
                $helper = new $class();
                $helper->setRenderer($this);
                $helpers[$type] = $helper;
            } else {
                $helper = $helpers[$type];
            }

            $useViewCache = true;
            if (isset($plugins['useCache'])) {
                foreach ($plugins['useCache'] as $pluginClass) {
                    $plugin = Kwf_Component_Plugin_Abstract::getInstance($pluginClass, $componentId);
                    // if one of the plugins return false no cache is used
                    $useViewCache = $plugin->useViewCache() && $useViewCache;
                }
            }

            $statType = null; /* for statistic: holds where the content comes from:
                                If it was load from cache, created and cached or has disabled viewCache */
            $content = null;
            $saveCache = false; //disable cache saving completely in preview
            if ($this->_enableCache) {
                $saveCache = true;
                $content = Kwf_Component_Cache::NO_CACHE;
                if ($helper->enableCache() && $useViewCache) { /* checks if cache is enabled
                                                               (not for eg. dynamic, partials or thru UseCache plugin) */
                    $content = Kwf_Component_Cache::getInstance()->load($componentId, $this->_getCacheName(), $type, $value);
                    $statType = 'cache'; //for statistic: was cached
                }
                if ($content == Kwf_Component_Cache::NO_CACHE) { /* if loaded cache was NO_CACHE or cache disabled
                                                                 (NO_CACHE is also default if not allowed to load):
                                                                 content is set to null => has to be rendered */
                    $content = null;
                    $saveCache = false;
                }
            }
            if (is_null($content)) {
                $content = $helper->render($componentId, $config); //Component default gets rendered
                if (isset($plugins['beforeCache'])) {
                    foreach ($plugins['beforeCache'] as $pluginClass) { //Plugins get possibility to manipulate html
                        $plugin = Kwf_Component_Plugin_Abstract::getInstance($pluginClass, $componentId);
                        $content = $plugin->processOutput($content);
                    }
                }
                if ($saveCache) {

                    $m = Kwf_Component_Cache::getInstance()->getModel('includes');
                    $s = $m->select()
                        ->whereEquals('component_id', $componentId)
                        ->whereEquals('type', $type);
                    $m->deleteRows($s);
                    if ($this->_includedComponents) {
                        $data = array();
                        foreach ($this->_includedComponents as $c) {
                            $cmp = Kwf_Component_Data_Root::getInstance()
                                ->getComponentById($componentId, array('ignoreVisible' => true));
                            $targetCmp = Kwf_Component_Data_Root::getInstance()
                                ->getComponentById($c['target_id'], array('ignoreVisible' => true));
                            if ($cmp->getPage() != $targetCmp->getPage()) {
                                $c['type'] = $type;
                                $c['component_id'] = $componentId;
                                $data[] = $c;
                            }
                        }
                        $m->import(Kwf_Model_Abstract::FORMAT_ARRAY, $data);
                        $this->_includedComponents = array();
                    }

                    //save rendered contents into view cache
                    //if viewCache=false Kwf_Component_Cache_Mysql saves Kwf_Component_Cache::NO_CACHE
                    $helper->saveCache($componentId, $this->_getCacheName(), $config, $value, $content);
                    $statType = 'nocache'; //for statistic: was not cached
                } else {
                    $statType = 'noviewcache'; //for statistic: view cache is disabled
                }
            }
            $content = $helper->renderCached($content, $componentId, $config); /* content is rendered.
                                                                    content can be directly from cache or generated,
                                                                    manipulated and saved in cache */

            if (isset($plugins['before'])) {
                foreach ($plugins['before'] as $pluginClass) {
                    if ($pass == 2) {
                        //in second pass it can be done now (won't be cached)
                        $plugin = Kwf_Component_Plugin_Abstract::getInstance($pluginClass, $componentId);
                        $content = $plugin->processOutput($content);
                    } else {
                        //in first pass the result will be cached, so we have to defer that to the second pass
                        $pluginNr++;
                        $content = "<pluginB $pluginNr $pluginClass $componentId>$content</pluginB $pluginNr>";
                    }
                }
            }

            if (isset($plugins['after'])) {
                foreach ($plugins['after'] as $pluginClass) {
                    //always has to be done last in second pass
                    $pluginNr++;
                    $content = "<pluginA $pluginNr $pluginClass $componentId>$content</pluginA $pluginNr>";
                }
            }

            if ($statType) Kwf_Benchmark::count("rendered $statType", $statId);

            $ret = substr($ret, 0, $start).$content.substr($ret, $end+1);


            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($componentId.' '.$type, microtime(true)-$startTime);
        }

        if ($pass == 2) {
            //execute plugins only in second pass, to not get cached
            $ret = $this->_executePlugins($ret, 'A');
        }

        return $ret;
    }

    public function getRenderComponent()
    {
        return $this->_renderComponent;
    }
}
