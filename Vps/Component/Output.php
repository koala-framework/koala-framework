<?php
class Vps_Component_Output
{
    private $_useCache = true;
    private $_cache;
   
    public function setCache(Vps_Component_Cache $cache)
    {
        $this->_cache = $cache;
    }
    
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = Vps_Component_Cache::getInstance();
        }
        return $this->_cache;
    }
    
    public function useCache($useCache)
    {
        $this->_useCache = $useCache;
    }
    
    public static function getInstance()
    {
        static $instance;
        if (!$instance) $instance = new self();
        return $instance;
    }
    
    public function renderMaster($component, $ignoreVisible = false, array $plugins = array())
    {
        return $this->render($component, $ignoreVisible, true, $plugins);
    }
    
    public function render($component, $ignoreVisible = false, $masterTemplate = false, array $plugins = array())
    {
        $componentId = $this->getCache()->getCacheIdFromComponentId($component->componentId, $masterTemplate);
        $pageId = $component->getPage() ? $component->getPage()->componentId : '';
        $pageId = $this->getCache()->getCacheIdFromComponentId($pageId);
        $this->getCache()->preload(array($componentId => $pageId));
        return $this->_render($component, $ignoreVisible, $masterTemplate, $plugins);
    }
    
    private function _render($component, $ignoreVisible = false, $masterTemplate = false, array $plugins = array())
    {
        $data = $this->_parseComponent($component, $ignoreVisible, $masterTemplate, $plugins);
        return $this->_renderComponent($data, $ignoreVisible);
    }
    
    private function _renderComponent($data, $ignoreVisible)
    {
        $ret = $data['content'];
        $preloadIds = array();
        foreach ($data['toLoad'] as $val) {
            $componentId = $this->getCache()->getCacheIdFromComponentId($val['componentId'], $val['masterTemplate']);
            $pageId = $this->getCache()->getCacheIdFromComponentId($val['pageId']);
            $preloadIds[$componentId] = $pageId;
        }
        foreach ($data['toLoadHasContent'] as $val) {
            $componentId = $this->getCache()->getCacheIdFromComponentId($val['componentId'], false, true);
            $pageId = $this->getCache()->getCacheIdFromComponentId($val['pageId']);
            $preloadIds[$componentId] = $pageId;
        }
        $this->getCache()->preload($preloadIds);
        foreach ($data['toLoadHasContent'] as $search => $val) {
            $content = $this->_renderHasContent($val, $ignoreVisible);
            $replace = $this->_parseTemplate($content, true, $ignoreVisible);
            $replace = $this->_renderComponent($replace, $ignoreVisible);
            $ret = str_replace($search, $replace, $ret);
        }
        foreach ($data['toLoad'] as $search => $val) {
            $replace = $this->_render($val, $ignoreVisible, $val['masterTemplate'], $val['plugins']);
            $ret = str_replace($search, $replace, $ret);
        }
        return $ret;
    }
    
    private function _parseComponent($component, $ignoreVisible = false, $masterTemplate = false, array $plugins = array())
    {
        if ($component instanceof Vps_Component_Data) {
            $componentClass = $component->componentClass;
            $componentId = $component->componentId;
            $pageId = $component->getPage() ? $component->getPage()->componentId : '';
        } else {
            $componentClass = $component['componentClass'];
            $componentId = $component['componentId'];
            $pageId = $component['pageId'];
            unset($component);
        }

        // Falls es Cache gibt, Cache holen
        $cacheId = $this->getCache()->getCacheIdFromComponentId($componentId, $masterTemplate);
        
        if ($this->_useCache($componentId, $componentClass)) {
            if ($this->getCache()->isLoaded($cacheId)) {
                Vps_Benchmark::count('rendered cache (preloaded)', $componentId.($masterTemplate?' (master)':''));
                $ret = $this->getCache()->load($cacheId);
            } else {
                if ($this->getCache()->shouldBeLoaded($cacheId)) {
                    $ret = $this->_renderNoCache($componentId, $ignoreVisible, $masterTemplate);
                } else {
                    $content = "{empty: $componentId}";
                    return array(
                        'content' => $content,
                        'toLoad' => array($content => array(
                            'componentClass' => $componentClass,
                            'componentId' => $componentId,
                            'pageId' => $pageId,
                            'masterTemplate' => $masterTemplate,
                            'plugins' => $plugins,
                        )),
                        'toLoadHasContent' => array(),
                        'notfound' => true
                    );
                }
            }
        } else {
            $ret = $this->_renderNoCache($componentId, $ignoreVisible, $masterTemplate);
        }
                
        //plugins _nach_ im cache speichern ausführen
        if ($ret) {
        foreach ($plugins as $p) {
            if (!$p) {
                throw new Vps_Exception("Invalid Plugin specified '$p'");
            }
            $p = new $p($componentId);
            $ret = $p->processOutput($ret);
        }
        }
        
        // content-Tags ersetzen
        return $this->_parseTemplate($ret, $ignoreVisible);
    }
    
    private function _parseTemplate($ret, $ignoreVisible)
    {
        $toLoad = array();
        $toLoadHasContent = array();
        preg_match_all("/{content: ([^ }]+) ([^ }]*) ([^ }]*)}(.*){content}/imsU", $ret, $contentMatches);
        foreach ($contentMatches[0] as $key => $search) {
            $c = array(
                'componentId' => $contentMatches[2][$key],
                'componentClass' => $contentMatches[1][$key],
                'pageId' => $contentMatches[3][$key],
                'content' => $contentMatches[4][$key]
            );
            
            $replace = $this->_renderHasContent($c, $ignoreVisible);
            if (!$replace) {
                $replace = "{hasContent " . $c['componentId'] . "}";
                $toLoadHasContent[$replace] = $c;
            }
            $ret = str_replace($search, $replace, $ret);
        }
        
        // nocache-Tags ersetzen
        preg_match_all('/{nocache: ([^ }]+) ([^ }]*) ([^ }]*) ?([^}]*)}/', $ret, $nocacheMatches);
        foreach ($nocacheMatches[0] as $key => $search) {
            $plugins = array();
            if ($nocacheMatches[4][$key]) {
                $plugins = explode(' ', $nocacheMatches[4][$key]);
            }
            $c = array(
                'componentClass' => $nocacheMatches[1][$key],
                'componentId' => $nocacheMatches[2][$key],
                'pageId' => $nocacheMatches[3][$key],
                'plugins' => $plugins,
                'masterTemplate' => false
            );
            $data = $this->_parseComponent($c, $ignoreVisible, false, $plugins);
            $ret = str_replace($search, $data['content'], $ret);   
            $toLoad = array_merge($toLoad, $data['toLoad']);
        }
        return array(
            'content' => $ret,
            'toLoad' => $toLoad,
            'toLoadHasContent' => $toLoadHasContent,
            'notfound' => false
        );
    }
    
    private function _renderHasContent($c, $ignoreVisible)
    {
        $componentId = $c['componentId'];
        $cacheId = $this->getCache()->getCacheIdFromComponentId($componentId, false, true);
        if ($this->_useCache($componentId, $c['componentClass'])) {
            if ($this->getCache()->isLoaded($cacheId)) {
                Vps_Benchmark::count('rendered cache (preloaded)', $componentId.' (hasContent)');
                $ret = $this->getCache()->load($cacheId);
            } else {
                if ($this->getCache()->shouldBeLoaded($cacheId)) {
                    $ret = $this->_renderHasContentNoCache($componentId, $ignoreVisible, $c['content']);
                } else {
                    $ret = false;
                }
            }
        } else {
            $ret = $this->_renderHasContentNoCache($componentId, $ignoreVisible, $c['content']);
        }
        return $ret;
    }
    
    private function _renderHasContentNoCache($componentId, $ignoreVisible, $content)
    {
        $component = $this->_getComponent($componentId, $ignoreVisible);
        if (!$component) {
            throw new Vps_Exception("Can't find component '$componentId'");
        }
        $cachedContent = $component->hasContent() ? $content : '';
        $cache = $this->getCache();
        $cacheId = $cache->getCacheIdFromComponentId($componentId, false, true);
        $cache->save($cachedContent, $cacheId, array(
            'componentClass'=>$component->componentClass,
            'pageId' => $cache->getCacheIdFromComponentId($component->getPage()->componentId)
        ));
        Vps_Benchmark::count('rendered nocache', $componentId.' (hasContent)');
        return $cachedContent;
    }
    
    private function _renderNoCache($componentId, $ignoreVisible, $masterTemplate)
    {
        $component = $this->_getComponent($componentId, $ignoreVisible);
        
        $cache = $this->getCache();
        $cacheId = $cache->getCacheIdFromComponentId($componentId, $masterTemplate);
        
        if ($component) {
            if ($masterTemplate) {
                $templateVars = array();
                $templateVars['component'] = $component;
                $templateVars['boxes'] = array();
                Vps_Debug::enable();
                foreach ($component->getChildBoxes() as $box) {
                    $templateVars['boxes'][$box->box] = $box;
                }
                $ret = $this->_renderView($masterTemplate, $templateVars);
            } else {
                $templateVars = $component->getComponent()->getTemplateVars();
                $template = Vpc_Admin::getComponentFile($component->componentClass, 'Component', 'tpl');
                if (!$template) {
                    throw new Vps_Exception("No Template found for '$component->componentClass'");
                }
        
                if (is_null($templateVars)) {
                    throw new Vps_Exception('getTemplateVars einer Komponenten gibt null zurück. return $vars; vergessen?');
                }
        
                $ret = $this->_renderView($template, $templateVars);
            }
            if ($cacheId) {
                $tags = array();
                $tags['componentClass'] = $component->componentClass;
                if ($component->getPage()) $tags['pageId'] = $cache->getCacheIdFromComponentId($component->getPage()->componentId);
                $cache->save($ret, $cacheId, $tags);
            }
        } else {
            $ret = "Component '$componentId' not found";
            //todo: throw error
        }
        Vps_Benchmark::count('rendered nocache', $componentId.($masterTemplate?' (master)':''));
        return $ret;
    }
    
    private function _useCache($componentId, $componentClass)
    {
        if (!$this->_useCache) return false;
        if (!Vpc_Abstract::getSetting($componentClass, 'viewCache')) {
            return false;
        }
        return true;
    }
    
    private function _getComponent($componentId, $ignoreVisible)
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => $ignoreVisible));
    }

    private function _renderView($template, $templateVars)
    {
        $view = new Vps_View_Component();
        $view->assign($templateVars);
        return $view->render($template);
    }
}
