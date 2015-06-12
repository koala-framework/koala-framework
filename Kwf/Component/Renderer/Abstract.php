<?php
abstract class Kwf_Component_Renderer_Abstract
{
    protected $_enableCache = null;

    protected $_includedComponents = array();
    protected $_minLifetime;

    private $_helpers = array();

    const PLUGIN_TYPE_AFTER = 'A';
    const PLUGIN_TYPE_BEFORE = 'B';
    const PLUGIN_TYPE_USECACHE = 'C';
    const PLUGIN_TYPE_REPLACE = 'R';

    public function includedComponent($targetComponentId, $targetType)
    {
        $this->_includedComponents[] = $targetComponentId;
    }

    public function __construct()
    {
        $this->_enableCache = true;
        if (Kwf_Component_Data_Root::getShowInvisible()) {
            $this->_enableCache = false;
        }
    }

    public function setEnableCache($enableCache)
    {
        if ($enableCache === null) throw new Kwf_Exception('expected boolean parameter');
        $this->_enableCache = $enableCache;
    }

    /**
     * Renders a single component without master
     */
    public function renderComponent($component, &$hasDynamicParts = false)
    {
        $helper = new Kwf_Component_View_Helper_Component();
        $helper->setRenderer($this);
        $content = $helper->component($component);

        $ret = $this->_renderPass2($content, $hasDynamicParts);
        Kwf_Component_Cache::getInstance()->writeBuffer();
        return $ret;
    }

    protected abstract function _getRendererName();

    //overridden by Renderer_Mail to use mail templates
    public function getTemplate(Kwf_Component_Data $component, $type)
    {
        $template = Kwc_Abstract::getTemplateFile($component->componentClass, $type);
        if (!$template) throw new Kwf_Exception("No $type-Template found for '{$component->componentClass}'");
        return $template;
    }

    public function render($ret)
    {
        $ret = $this->_renderPass2($ret, $hasDynamicParts);
        return $ret;
    }

    private function _findAndExecutePlugins($ret, $pluginType, &$hasDynamicParts)
    {
        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        while (($start = strpos($ret, '<plugin'.$pluginType.' ')) !== false) {
            $hasDynamicParts = true;
            $startEnd = strpos($ret, '>', $start);
            $args = explode(' ', substr($ret, $start+9, $startEnd-$start-9));
            $end = strpos($ret, '</plugin'.$pluginType.' '.$args[0].'>');
            $content = substr($ret, $startEnd+1, $end-$startEnd-1);
            if ($benchmarkEnabled) $startTime = microtime(true);
            $plugin = Kwf_Component_Plugin_Abstract::getInstance($args[1], $args[2]);
            if ($pluginType == self::PLUGIN_TYPE_USECACHE) {
                if (!$plugin->useViewCache($this)) {
                    $content = $this->_getHelper('component')->render($args[2], array());
                }
            } else if ($pluginType == self::PLUGIN_TYPE_AFTER || $pluginType == self::PLUGIN_TYPE_BEFORE) {
                $content = $plugin->processOutput($content, $this);
            } else if ($pluginType == self::PLUGIN_TYPE_REPLACE) {
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

    private function _findSinglePlugin($pluginType, $content)
    {
        if (substr($content, 0, 9) == '<plugin'.$pluginType.' ') {
            $pluginEnd = strpos($content, '>');
            $args = explode(' ', substr($content, 10, $pluginEnd-10));
            $content = substr($content, $pluginEnd+1, strrpos($content, '</')-$pluginEnd-1);
            return array(
                'plugin' => Kwf_Component_Plugin_Abstract::getInstance($args[1], $args[2]),
                'content' => $content
            );
        }
        return null;
    }

    private function _addPluginPlaceholders($content, $plugins, $componentId, $pluginTypes)
    {
        static $pluginNr = 0;
        if (in_array('before', $pluginTypes) && isset($plugins['before'])) {
            foreach ($plugins['before'] as $pluginClass) {
                $pluginNr++;
                $content = "<plugin".self::PLUGIN_TYPE_BEFORE." $pluginNr $pluginClass $componentId>$content</plugin".self::PLUGIN_TYPE_BEFORE." $pluginNr>";
            }
        }

        if (in_array('after', $pluginTypes) && isset($plugins['after'])) {
            foreach ($plugins['after'] as $pluginClass) {
                $pluginNr++;
                $content = "<plugin".self::PLUGIN_TYPE_AFTER." $pluginNr $pluginClass $componentId>$content</plugin".self::PLUGIN_TYPE_AFTER." $pluginNr>";
            }
        }

        if (in_array('replace', $pluginTypes) && isset($plugins['replace'])) {
            foreach ($plugins['replace'] as $pluginClass) {
                $pluginNr++;
                $content = "<plugin".self::PLUGIN_TYPE_REPLACE." $pluginNr $pluginClass $componentId>$content</plugin".self::PLUGIN_TYPE_REPLACE." $pluginNr>";
            }
        }

        if (in_array('useCache', $pluginTypes) && isset($plugins['useCache'])) {
            foreach ($plugins['useCache'] as $pluginClass) {
                $pluginNr++;
                $content = "<plugin".self::PLUGIN_TYPE_USECACHE." $pluginNr $pluginClass $componentId>$content</plugin".self::PLUGIN_TYPE_USECACHE." $pluginNr>";
            }
        }
        return $content;
    }

    protected static function _getGroupedViewPlugins(Kwf_Component_Data $component)
    {
        $plugins = array();
        foreach ($component->getComponent()->getActiveViewPlugins() as $p) {
            if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewBeforeCache')) {
                $plugins['beforeCache'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewBeforeChildRender')) {
                $plugins['before'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewAfterChildRender')) {
                $plugins['after'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_ViewReplace')) {
                $plugins['replace'][] = $p;
            } else if (is_instance_of($p, 'Kwf_Component_Plugin_Interface_UseViewCache')) {
                $plugins['useCache'][] = $p;
            }
        }
        return $plugins;
    }

    private function _executePlugins($content, $plugins, $componentId, $pluginTypes)
    {
        if (in_array('replace', $pluginTypes) && isset($plugins['replace'])) {
            foreach ($plugins['replace'] as $pluginClass) {
                $plugin = Kwf_Component_Plugin_Abstract::getInstance($pluginClass, $componentId);
                $r = $plugin->replaceOutput($this);
                if ($r !== false) {
                    $content = $r;
                }
            }
        }
        if (in_array('beforeCache', $pluginTypes) && isset($plugins['beforeCache'])) {
            foreach ($plugins['beforeCache'] as $pluginClass) {
                $plugin = Kwf_Component_Plugin_Abstract::getInstance($pluginClass, $componentId);
                $content = $plugin->processOutput($content, $this);
            }
        }
        if (in_array('before', $pluginTypes) && isset($plugins['before'])) {
            foreach ($plugins['before'] as $pluginClass) {
                $plugin = Kwf_Component_Plugin_Abstract::getInstance($pluginClass, $componentId);
                $content = $plugin->processOutput($content, $this);
            }
        }
        return $content;
    }

    private function _renderUncached($componentId, $type, $config)
    {
        $plugins = array();
        if ($type == 'component') {
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($componentId, array('ignoreVisible'=>true));
            if (!$c) {
                throw new Kwf_Exception("Didn't get '$componentId'");
            }
            $plugins = $this->_getGroupedViewPlugins($c);
        }
        $content = $this->_executePlugins(null, $plugins, $componentId, array('replace'));

        if (is_null($content)) {
            $content = $this->_getHelper($type)->render($componentId, $config);
            $content = $this->_executePlugins($content, $plugins, $componentId, array('beforeCache'));
        }
        $content = $this->_executePlugins($content, $plugins, $componentId, array('before'));
        $content = $this->_addPluginPlaceholders($content, $plugins, $componentId, array('after'));

        return $content;
    }

    private function _renderAndCache($componentId, $type, $value, $config, $addPluginPlaceholders, &$cacheSaved = true)
    {
        $plugins = array();
        if ($type == 'component') {
            $c = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($componentId, array('ignoreVisible'=>true));
            $plugins = $this->_getGroupedViewPlugins($c);
        }

        $cacheSaved = true;
        if (isset($plugins['useCache'])) {
            foreach ($plugins['useCache'] as $p) {
                $p = Kwf_Component_Plugin_Abstract::getInstance($p, $componentId);
                if (!$p->useViewCache($this)) {
                    $cacheSaved = false;
                }
            }
        }
        $content = null;

        if (!$cacheSaved || !$this->_enableCache) {
            //we can't save cache replace before rendering
            $content = $this->_executePlugins($content, $plugins, $componentId, array('replace'));
        }

        if (is_null($content)) {
            //render if not replaced
            $content = $this->_getHelper($type)->render($componentId, $config);
            $content = $this->_executePlugins($content, $plugins, $componentId, array('beforeCache'));
        }

        //after plugin always as placeholder to execute later
        $content = $this->_addPluginPlaceholders($content, $plugins, $componentId, array('after'));

        $cacheContent = $content;

        if ($cacheSaved && $this->_enableCache) {
            $viewCacheSettings = $this->_getHelper($type)->getViewCacheSettings($componentId);
            if (!is_bool($viewCacheSettings['enabled'])) {
                //dynamic (callback) cache enabled setting
                if (!call_user_func_array($viewCacheSettings['enabled']['callback'], $viewCacheSettings['enabled']['args'])) {
                    $cacheSaved = false;
                } else {
                    $cacheContent = '<useCacheDynamic '.$componentId.
                                    ' '.$type.
                                    ' '.base64_encode(serialize($config)).
                                    ' '.serialize($viewCacheSettings['enabled']).
                                '>'.$cacheContent.'</useCacheDynamic '.$componentId.'>';
                }
            }
        }

        if ($cacheSaved && $this->_enableCache) {
            //if saving to cache add placeholders
            $cacheContent = $this->_addPluginPlaceholders($cacheContent, $plugins, $componentId, array('replace', 'before', 'useCache'));
            $this->_cacheSave($componentId, $type, $value, $cacheContent);

            if ($addPluginPlaceholders) {
                //return cached content to be able to re-execute plugins in pass2
                $content = $cacheContent;
            } else {
                $content = $this->_executePlugins($content, $plugins, $componentId, array('before', 'replace'));
            }
        } else {
            //if not saving to cache execute plugins now (replace and useCache are already executed)
            $content = $this->_executePlugins($content, $plugins, $componentId, array('before'));
        }

        return $content;
    }


    /**
     * Render components (ie. expand <kwc ...>)
     *
     * Pass 1 for content that can be stored in fullPage cache,
     * 2 for everything else. 2 includes 1, so calling just with 2 also works
     * @param string render content
     * @param bool if returned contents is cacheable
     */
    protected function _renderPass1($ret, &$pass1Cacheable = true)
    {
        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        $offset = 0;
        while ($target = $this->_getNextRenderTarget($ret, 1, $offset)) {
            if ($benchmarkEnabled) $startTime = microtime(true);
            $helper = $this->_getHelper($target['type']);

            $statType = null;

            $content = null;
            if ($this->_enableCache) {
                $content = $this->_cacheLoad($target['componentId'], $target['type'], $target['value']);
            }
            if (!is_null($content)) {
                //cache hit
                $statType = 'hit';

                //look for UseViewCache plugin in $content
                if ($p = $this->_findSinglePlugin(self::PLUGIN_TYPE_USECACHE, $content)) {
                    if (!$p['plugin']->useViewCache($this)) {
                        //useViewCache=false: re-render but don't cache
                        $pass1Cacheable = false;
                        $content = $this->_renderUncached($target['componentId'], $target['type'], $target['config']);
                    } else {
                        //useViewCache=true
                        //continue with <pluginC in $content (so it can be cached in fullPage cache)
                    }
                }
            } else {
                //cache miss
                $statType = 'miss';

                $cacheSaved = true;
                $content = $this->_renderAndCache(
                    $target['componentId'], $target['type'], $target['value'], $target['config'],
                    true, //addPluginPlaceholders (so it can be cached in fullPage cache)
                    $cacheSaved //will be set to false if we can't use fullPage cache because eg a UseViewCache plugin returned false
                );
                if (!$cacheSaved) $pass1Cacheable = false;
            }

            //if $pass1Cacheable=true $content must include all <plugin?s
            //if $pass1Cacheable=false $contents must only include <pluginA and all other plugins must be executed already

            $content = $helper->renderCached($content, $target['componentId'], $target['config']);

            $ret = substr($ret, 0, $target['start']).$content.substr($ret, $target['end']+1);

            if ($statType) {
                if ($benchmarkEnabled) Kwf_Benchmark::count("rendered $statType", $target['statId']);
                Kwf_Benchmark::countLog('render-'.$statType);
            }

            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($target['componentId'].' '.$target['type'], microtime(true)-$startTime);
        }
        return $ret;
    }

    private function _findAndExecuteUseCacheDynamic($ret, $hasDynamicParts)
    {
        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        //'<useCacheDynamic '.$componentId.' '.serialize($viewCacheSettings['enabled']).'>'.$content.'</useCacheDynamic '.$componentId.'>'
        while (($start = strpos($ret, '<useCacheDynamic ')) !== false) {
            $hasDynamicParts = true;
            $startEnd = strpos($ret, '>', $start);
            $args = explode(' ', substr($ret, $start+17, $startEnd-$start-17));
            //args: 0: componentId, 1: type, 2: config, 3: serialized cache enabled callback fn
            $end = strpos($ret, '</useCacheDynamic '.$args[0].'>');
            $content = substr($ret, $startEnd+1, $end-$startEnd-1);
            if ($benchmarkEnabled) $startTime = microtime(true);
            $cb = unserialize($args[3]);
            if (!call_user_func_array($cb['callback'], $cb['args'])) {
                //cache disabled, re-render
                $content = $this->_getHelper($args[1])->render($args[0], unserialize(base64_decode($args[2])));
            }
            $ret = substr($ret, 0, $start).$content.substr($ret, $end+19+strlen($args[0]));

            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint('useCacheDynamic '.$args[1], microtime(true)-$startTime);
        }
        return $ret;
    }

    /**
     * Render components (ie. expand <kwc ...>)
     *
     * Pass 1 for content that can be stored in fullPage cache,
     * 2 for everything else. 2 includes 1, so calling just with 2 also works
     * @param string render content
     */
    protected function _renderPass2($ret, &$hasDynamicParts = false)
    {
        //execute all plugins that where added in pass 1
        $ret = $this->_findAndExecutePlugins($ret, self::PLUGIN_TYPE_USECACHE, $hasDynamicParts);
        $ret = $this->_findAndExecutePlugins($ret, self::PLUGIN_TYPE_BEFORE, $hasDynamicParts);
        $ret = $this->_findAndExecutePlugins($ret, self::PLUGIN_TYPE_REPLACE, $hasDynamicParts);
        $ret = $this->_findAndExecuteUseCacheDynamic($ret, $hasDynamicParts);

        static $benchmarkEnabled;
        if (!isset($benchmarkEnabled)) $benchmarkEnabled = Kwf_Benchmark::isEnabled();

        $offset = 0;
        while ($target = $this->_getNextRenderTarget($ret, 2, $offset)) {
            if ($benchmarkEnabled) $startTime = microtime(true);

            if ($target['type'] == 'dynamic' && $target['config']['class'] == 'Kwf_Component_Dynamic_SessionToken' && !Kwf_Setup::hasAuthedUser()) {
                $hasDynamicParts = true;
                //yes, this is cheating, but a very common case that's worth optimizing using this hack
                $ret = substr($ret, 0, $target['start']).''.substr($ret, $target['end']+1);
                continue;
            }

            $helper = $this->_getHelper($target['type']);

            $statType = null;

            $content = null;
            if ($this->_enableCache && $target['isCacheable']) {
                $content = $this->_cacheLoad($target['componentId'], $target['type'], $target['value']);
            }
            if (!is_null($content)) {
                //cache hit
                $statType = 'hit';

                //look for UseViewCache plugin in $content
                if ($p = $this->_findSinglePlugin(self::PLUGIN_TYPE_USECACHE, $content)) {
                    $hasDynamicParts = true;
                    if (!$p['plugin']->useViewCache($this)) {
                        //re-render, without <pluginC
                        $content = $this->_renderUncached($target['componentId'], $target['type'], $target['config']);
                    } else {
                        //continue with content
                        $content = $p['content'];
                    }
                } else {
                    //execute replace and before plugin
                    if ($p = $this->_findSinglePlugin(self::PLUGIN_TYPE_REPLACE, $content)) {
                        $hasDynamicParts = true;
                        $r = $p['plugin']->replaceOutput($this);
                        if ($r !== false) {
                            $content = $r;
                        } else {
                            $content = $p['content'];
                        }
                    }
                    $content = $this->_findAndExecutePlugins($content, self::PLUGIN_TYPE_BEFORE, $hasDynamicParts);
                }

                $content = $this->_findAndExecuteUseCacheDynamic($content, $hasDynamicParts);

            } else {
                if ($this->_enableCache && $target['isCacheable']) {
                    //cache miss
                    $statType = 'miss';
                    $content = $this->_renderAndCache($target['componentId'], $target['type'], $target['value'], $target['config'],
                        false //don't add plugin placeholders, execute them
                    );

                } else {
                    $hasDynamicParts = true;
                    //view cache disabled
                    $statType = 'noviewcache';
                    $content = $this->_renderUncached($target['componentId'], $target['type'], $target['config']);
                }
            }

            $content = $helper->renderCached($content, $target['componentId'], $target['config']);

            $ret = substr($ret, 0, $target['start']).$content.substr($ret, $target['end']+1);

            if ($statType) {
                if ($benchmarkEnabled) Kwf_Benchmark::count("rendered $statType", $target['statId']);
                Kwf_Benchmark::countLog('render-'.$statType);
            }

            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($target['componentId'].' '.$target['type'], microtime(true)-$startTime);
        }

        //execute Render Cached Dynamic, used eg for callback link modifier in componentLink
        while (($start = strpos($ret, '<rcd ')) !== false) {
            $hasDynamicParts = true;
            $startEnd = strpos($ret, '>', $start);
            $args = explode(' ', substr($ret, $start+5, $startEnd-$start-5));
            $end = strpos($ret, '</rcd '.$args[0].'>');
            $content = substr($ret, $startEnd+1, $end-$startEnd-1);
            if ($benchmarkEnabled) $startTime = microtime(true);
            $componentId = $args[0];
            $type = $args[1];
            $settings = json_decode($args[2], true);
            $content = $this->_getHelper($type)->renderCachedDynamic($content, $componentId, $settings);
            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint("renderCachedDynamic $type $componentId", microtime(true)-$startTime);
            $ret = substr($ret, 0, $start).$content.substr($ret, $end+7+strlen($args[0]));
        }

        $ret = $this->_findAndExecutePlugins($ret, self::PLUGIN_TYPE_AFTER, $hasDynamicParts);

        return $ret;
    }

    private function _cacheLoad($componentId, $type, $value)
    {
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
            return $data['contents'];
        }
        return null;
    }


    private function _cacheSave($componentId, $type, $value, $content)
    {
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

        $settings = $this->_getHelper($type)->getViewCacheSettings($componentId);
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
            isset($settings['cacheTag']) ? $settings['cacheTag'] : '',
            isset($settings['lifetime']) ? $settings['lifetime'] : null
        );
        if ($settings['lifetime']) {
            if (is_null($this->_minLifetime)) {
                $this->_minLifetime = $settings['lifetime'];
            } else {
                $this->_minLifetime = min($this->_minLifetime, $settings['lifetime']);
            }
        }
    }

    public function getHelper($type)
    {
        return $this->_getHelper($type);
    }

    private function _getHelper($type)
    {
        if (!isset($this->_helpers[$type])) {
            $class = 'Kwf_Component_View_Helper_' . ucfirst($type);
            $helper = new $class();
            $helper->setRenderer($this);
            $this->_helpers[$type] = $helper;
        }
        return $this->_helpers[$type];
    }

    private function _getNextRenderTarget($ret, $pass, &$offset)
    {
        while (($start = strpos($ret, '<kwc', $offset)) !== false) {
            $p = substr($ret, $start+4, 1);
            if ($pass == 1 && $p == 2) {
                $offset = $start+1;
                continue; //matches $pass? (first char after <kwc)
            }
            $end = strpos($ret, '>', $start);

            $args = explode(' ', substr($ret, $start+6, $end-$start-6));
            $type = $args[0];
            $componentId = $args[1];
            $value = (string)$args[2]; // Bei Partial partialId oder bei master component_id zu der das master gehört
            $config = $args[3];
            $config = $config != '' ? unserialize(base64_decode($config)) : array();
            $statId = $componentId;
            if ($value) $statId .= " ($value)";
            if ($type != 'component') $statId .= ': ' . $type;

            return array(
                'start' => $start,
                'end' => $end,
                'type' => $type,
                'componentId' => $componentId,
                'value' => $value,
                'config' => $config,
                'statId' => $statId,
                'isCacheable' => ($p == 1)
            );
        }
    }
}
