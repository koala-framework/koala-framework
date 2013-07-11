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

    /**
     * Render components (ie. expand {cc ...})
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
            while (preg_match('/{pluginB (\d) ([^}]*) ([^}]*)}(.*){\/pluginB \\1}/s', $ret, $matches)) {
                $plugin = Kwf_Component_Plugin_Abstract::getInstance($matches[2], $matches[3]);
                $content = $plugin->processOutput($matches[4]);
                $ret = str_replace($matches[0], $content, $ret);
            }
        }

        if ($pass == 1) {
            $passExp = '1';
        } else if ($pass == 2) {
            $passExp = '[12]';
        } else {
            throw new Kwf_Exception("Invalid pass");
        }
        //                  {cc1 type    : componentId (value)      {plugins}    config}
        while (preg_match('/{cc'.$passExp.' ([a-z]+): ([^ \[}\(]+)(\([^ }]+\))?({[^}]+})?( [^}]*)}/i', $ret, $matches)) {
            if ($benchmarkEnabled) $startTime = microtime(true);
            $type = $matches[1];
            $componentId = trim($matches[2]);
            $value = (string)trim($matches[3]); // Bei Partial partialId oder bei master component_id zu der das master gehört
            if ($value) $value = substr($value, 1, -1);
            $plugins = json_decode($matches[4], true);
            if (!$plugins) $plugins = array();
            $config = trim($matches[5]);
            $config = $config != '' ? unserialize(base64_decode($config)) : array();

            if (isset($plugins['replace'])) {
                foreach ($plugins['replace'] as $pluginClass) {
                    $plugin = Kwf_Component_Plugin_Abstract::getInstance($pluginClass, $componentId);
                    $content = $plugin->replaceOutput();
                    if ($content !== false) {
                        if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($componentId.' plugin', microtime(true)-$startTime);
                        $ret = str_replace($matches[0], $content, $ret);
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
                        $content = "{pluginB $pluginNr $pluginClass $componentId}$content{/pluginB $pluginNr}";
                    }
                }
            }

            if (isset($plugins['after'])) {
                foreach ($plugins['after'] as $pluginClass) {
                    //always has to be done last in second pass
                    $pluginNr++;
                    $content = "{pluginA $pluginNr $pluginClass $componentId}$content{/pluginA $pluginNr}";
                }
            }

            if ($statType) Kwf_Benchmark::count("rendered $statType", $statId);
            $ret = str_replace($matches[0], $content, $ret);

            if ($benchmarkEnabled) Kwf_Benchmark::subCheckpoint($componentId.' '.$type, microtime(true)-$startTime);
        }

        if ($pass == 2) {
            //execute plugins only in second pass, to not get cached
            while (preg_match('/{pluginA (\d) ([^}]*) ([^}]*)}(.*){\/pluginA \\1}/s', $ret, $matches)) {
                $plugin = Kwf_Component_Plugin_Abstract::getInstance($matches[2], $matches[3]);
                $content = $plugin->processOutput($matches[4]);
                $ret = str_replace($matches[0], $content, $ret);
            }
        }

        return $ret;
    }

    public function getRenderComponent()
    {
        return $this->_renderComponent;
    }
}
