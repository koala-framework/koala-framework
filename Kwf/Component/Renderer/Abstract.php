<?php
abstract class Kwf_Component_Renderer_Abstract
{
    protected $_enableCache = null;
    protected $_renderComponent;

    protected $_includedComponents = array();
    protected $_minLifetime;

    public function includedComponent($targetComponentId, $targetType)
    {
        $this->_includedComponents[] = $targetComponentId;
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

        $helper = new Kwf_Component_View_Helper_Component();
        $helper->setRenderer($this);
        $content = $helper->component($component);

        $ret = $this->_render(2, $content);
        Kwf_Component_Cache::getInstance()->writeBuffer();
        return $ret;
    }

    protected abstract function _getRendererName();

    public function getTemplate(Kwf_Component_Data $component, $type)
    {
        $template = Kwc_Abstract::getTemplateFile($component->componentClass, $type);
        if (!$template) throw new Kwf_Exception("No $type-Template found for '{$component->componentClass}'");
        return $template;
    }

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
            if ($pluginType == 'C') {
                if (!$plugin->useViewCache($this)) {
                    $c = Kwf_Component_Data_Root::getInstance()->getComponentById($args[2], array('ignoreVisible'=>true));
                    $content = $this->renderComponent($c);
                }
            } else if ($pluginType == 'A' || $pluginType == 'B') {
                $content = $plugin->processOutput($content, $this);
            } else if ($pluginType == 'R') {
                $c = $plugin->replaceOutput($this);
                if ($c !== false) {
                    $content = $c;
                }
            }

            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint('plugin '.$args[1], microtime(true)-$startTime);
            $ret = substr($ret, 0, $start).$content.substr($ret, $end+11+strlen($args[0]));
        }
        return $ret;
    }

    /**
     * Render components (ie. expand <kwc ...>)
     *
     * @param int render pass; 1 or 2: 1 for content that can be stored in fullPage cache,
                                       2 for everything else. 2 includes 1, so calling just with 2 also works
     * @param string render content
     */
    protected function _render($pass, $ret, &$pass1Cacheable = true)
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
                continue; //matches $pass? (first char after <kwc)
            }
            if ($benchmarkEnabled) $startTime = microtime(true);
            $end = strpos($ret, '>', $start);

            $isCacheable = $p==1; //only load <kwc1 placeholders
            $args = explode(' ', substr($ret, $start+6, $end-$start-6));
            $type = $args[0];
            $componentId = $args[1];
            $value = (string)$args[2]; // Bei Partial partialId oder bei master component_id zu der das master gehört
            $plugins = json_decode($args[3], true);
            $config = $args[4];
            $config = $config != '' ? unserialize(base64_decode($config)) : array();
            if ($type == 'dynamic' && $config['class'] == 'Kwf_Component_Dynamic_SessionToken' && !Kwf_Setup::hasAuthedUser()) {
                //yes, this is cheating, but a very common case that's worth optimizing using this hack
                $ret = substr($ret, 0, $start).''.substr($ret, $end+1);
                continue;
            }
            if (isset($plugins['replace']) && $pass==2) {
                foreach ($plugins['replace'] as $pluginClass) {
                    $plugin = Kwf_Component_Plugin_Abstract::getInstance($pluginClass, $componentId);
                    $content = $plugin->replaceOutput($this);
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

            $useViewCache = $isCacheable;
            if ($useViewCache && isset($plugins['useCache'])) { //decide here not to use view cache; pass 1 additionally handled below
                foreach ($plugins['useCache'] as $pluginClass) {
                    $plugin = Kwf_Component_Plugin_Abstract::getInstance($pluginClass, $componentId);
                    // if one of the plugins return false no cache is used
                    $useViewCache = $plugin->useViewCache($this) && $useViewCache;
                    if (!$useViewCache && $pass==1) {
                        $pass1Cacheable = false;
                    }
                }
            }

            $statType = null; /* for statistic: holds where the content comes from:
                                If it was load from cache, created and cached or has disabled viewCache */
            $content = null;
            $saveCache = false; //disable cache saving completely in preview
            if ($this->_enableCache) {
                $saveCache = true;
                $content = Kwf_Component_Cache::NO_CACHE;
                if ($useViewCache) { //checks if cache is enabled (not thru UseCache plugin)
                    $data = Kwf_Component_Cache::getInstance()->loadWithMetadata($componentId, $this->_getRendererName(), $type, $value);
                    if ($data) {
                        if ($data['expire']) {
                            $lifetime = $data['expire']-time();
                            if ($lifetime < -1) {
                                throw new Kwf_Exception("invalid lifetime: $lifetime");
                            }
                            if (is_null($this->_minLifetime)) {
                                $this->_minLifetime = $lifetime;
                            } else {
                                $this->_minLifetime = min($this->_minLifetime, $lifetime);
                            }
                        }
                        $content = $data['contents'];
                    } else {
                        $content = null;
                    }

                    $statType = 'hit'; //for statistic: was cached
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
                        $content = $plugin->processOutput($content, $this);
                    }
                }

                if ($saveCache) {

                    $m = Kwf_Component_Cache::getInstance()->getModel('includes');
                    $s = $m->select()
                        ->whereEquals('component_id', $componentId)
                        ->whereEquals('type', $type);
                    $existingTargetIds = array();
                    foreach ($m->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, array('columns'=>array('target_id'))) as $i) {
                        $existingTargetIds[] = $i['target_id'];
                    }
                    $newTargetIds = array();
                    if ($this->_includedComponents) {
                        $data = array();
                        foreach ($this->_includedComponents as $includedComponentId) {
                            $cmp = Kwf_Component_Data_Root::getInstance()
                                ->getComponentById($componentId, array('ignoreVisible' => true));
                            $targetCmp = Kwf_Component_Data_Root::getInstance()
                                ->getComponentById($includedComponentId, array('ignoreVisible' => true));
                            if ($cmp->getPage() !== $targetCmp->getPage()) {
                                if (!in_array($includedComponentId, $existingTargetIds)) {
                                    $c = array(
                                        'target_id' => $includedComponentId,
                                        'type' => $type,
                                        'component_id' => $componentId,
                                    );
                                    $data[] = $c;
                                }
                                $newTargetIds[] = $includedComponentId;
                            }
                        }
                        $m->import(Kwf_Model_Abstract::FORMAT_ARRAY, $data);
                    }
                    $this->_includedComponents = array();
                    $diffTargetIds = array_diff($existingTargetIds, $newTargetIds);
                    if ($diffTargetIds) {
                        //delete not anymore included
                        $m = Kwf_Component_Cache::getInstance()->getModel('includes');
                        $s = $m->select()
                            ->whereEquals('component_id', $componentId)
                            ->whereEquals('type', $type)
                            ->whereEquals('target_id', $diffTargetIds);
                        $m->deleteRows($s);
                    }

                    //save rendered contents into view cache
                    $cacheContent = $content;
                    $component = Kwf_Component_Data_Root::getInstance()
                        ->getComponentById($componentId, array('ignoreVisible' => true));
                    if (!$component) throw new Kwf_Exception("Can't find component '$componentId' for rendering");

                    $settings = $helper->getViewCacheSettings($componentId);
                    if (!$settings['enabled']) {
                        //something is very wrong
                        throw new Kwf_Exception('$isCacheable should be false if the view cache is disabled for this helper');
                        $cacheContent = Kwf_Component_Cache::NO_CACHE;
                    }
                    // Content-Cache
                    Kwf_Component_Cache::getInstance()->save(
                        $component,
                        $cacheContent,
                        $this->_getRendererName(),
                        $type,
                        $value,
                        isset($settings['lifetime']) ? $settings['lifetime'] : null
                    );
                    if ($settings['lifetime']) {
                        if (is_null($this->_minLifetime)) {
                            $this->_minLifetime = $settings['lifetime'];
                        } else {
                            $this->_minLifetime = min($this->_minLifetime, $settings['lifetime']);
                        }
                    }

                    $statType = 'miss'; //for statistic: was not cached
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
                        $content = $plugin->processOutput($content, $this);
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

            if (isset($plugins['useCache']) && $pass==1) { //in pass 1 wrap into <pluginC to replace with non-cached content if plugin decides so
                foreach ($plugins['useCache'] as $pluginClass) {
                    //always has to be done last in second pass
                    $pluginNr++;
                    $content = "<pluginC $pluginNr $pluginClass $componentId>$content</pluginC $pluginNr>";
                }
            }

            if (isset($plugins['replace']) && $pass==1) { //in pass 1 wrap into <pluginR to replace with plugin output
                foreach ($plugins['replace'] as $pluginClass) {
                    //always has to be done last in second pass
                    $pluginNr++;
                    $content = "<pluginR $pluginNr $pluginClass $componentId>$content</pluginR $pluginNr>";
                }
            }


            if ($statType) {
                if ($benchmarkEnabled) Kwf_Benchmark::count("rendered $statType", $statId);
                Kwf_Benchmark::countLog('render-'.$statType);
            }

            $ret = substr($ret, 0, $start).$content.substr($ret, $end+1);


            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($componentId.' '.$type, microtime(true)-$startTime);
        }

        if ($pass == 2) {
            //execute plugins only in second pass, to not get cached
            $ret = $this->_executePlugins($ret, 'A');

            //replace content where useCache plugin return false with uncached content
            $ret = $this->_executePlugins($ret, 'C');

            //replace content with plugin output
            $ret = $this->_executePlugins($ret, 'R');


            //execute Render Cached Dynamic, used eg for callback link modifier in componentLink
            while (($start = strpos($ret, '<rcd ')) !== false) {
                $startEnd = strpos($ret, '>', $start);
                $args = explode(' ', substr($ret, $start+5, $startEnd-$start-5));
                $end = strpos($ret, '</rcd '.$args[0].'>');
                $content = substr($ret, $startEnd+1, $end-$startEnd-1);
                if ($benchmarkEnabled) $startTime = microtime(true);
                $componentId = $args[0];
                $type = $args[1];
                $settings = json_decode($args[2], true);
                if (!isset($helpers[$type])) {
                    $class = 'Kwf_Component_View_Helper_' . ucfirst($type);
                    $helper = new $class();
                    $helper->setRenderer($this);
                    $helpers[$type] = $helper;
                } else {
                    $helper = $helpers[$type];
                }
                $content = $helper->renderCachedDynamic($content, $componentId, $settings);
                if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint("renderCachedDynamic $type $componentId", microtime(true)-$startTime);
                $ret = substr($ret, 0, $start).$content.substr($ret, $end+7+strlen($args[0]));
            }
        }

        return $ret;
    }

    public function getRenderComponent()
    {
        return $this->_renderComponent;
    }
}
