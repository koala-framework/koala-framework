<?php
class Vps_View_Component extends Vps_View
{
    private static $_loadedIds = array();
    private static $_componentCache = array();
   
    public function init()
    {
        parent::init();
        $this->addScriptPath('application/views');
    }

    public static function renderMasterComponent(Vps_Component_Data $component, $masterTemplate = null, $ignoreVisible = false)
    {
        if (!$masterTemplate) $masterTemplate = 'application/views/master/default.tpl';
        self::preloadComponentCache(array($component->componentId . '-master' => $component->getPage()->componentId));
        return self::renderComponent($component, $ignoreVisible, $masterTemplate);
    }
    
    public static function renderComponent($component, $ignoreVisible = false, $masterTemplate = false, array $plugins = array())
    {
        $data = self::_parseComponent($component, $ignoreVisible, $masterTemplate);
        return self::_renderComponent($data, $ignoreVisible);
    }
    
    private static function _renderComponent($data, $ignoreVisible)
    {
        $ret = $data['content'];
        $preloadIds = array();
        foreach ($data['toLoad'] as $val) {
            $preloadIds[$val['componentId']] = $val['pageId'];
        }
        foreach ($data['toLoadHasContent'] as $val) {
            $preloadIds[$val['componentId']] = $val['pageId'];
        }
        self::preloadComponentCache($preloadIds);
        foreach ($data['toLoadHasContent'] as $search => $val) {
            $content = self::_renderHasContent($val, $ignoreVisible);
            $replace = self::_parseTemplate($content, true, $ignoreVisible);
            $replace = self::_renderComponent($replace, $ignoreVisible);
            $ret = str_replace($search, $replace, $ret);
        }
        foreach ($data['toLoad'] as $search => $val) {
            $replace = self::renderComponent($val, $ignoreVisible);
            $ret = str_replace($search, $replace, $ret);
        }
        return $ret;
    }
    
    private static function _parseComponent($component, $ignoreVisible = false, $masterTemplate = false, array $plugins = array())
    {
        if ($component instanceof Vps_Component_Data) {
            $componentClass = $component->componentClass;
            $componentId = $component->componentId;
            $pageId = $component->getPage()->componentId;
        } else {
            $componentClass = $component['componentClass'];
            $componentId = $component['componentId'];
            $pageId = $component['pageId'];
            unset($component);
        }
        
        // Falls es Cache gibt, Cache holen
        $cacheId = Vps_Component_Cache::getInstance()->getCacheIdFromComponentId($componentId, $masterTemplate);
        if (self::_hasCache($componentId, $componentClass)) {
            if (isset(self::$_componentCache[$cacheId])) {
                Vps_Benchmark::count('rendered cache (preloaded)', $componentId.($masterTemplate?' (master)':''));
                $ret = self::$_componentCache[$cacheId];
            } else {
                $loadId = $componentId;
                if ($masterTemplate) $loadId .= '-master';
                if (self::_shouldBeLoaded($loadId)) {
                    $ret = self::_renderNoCache($componentId, $ignoreVisible, $masterTemplate);
                } else {
                    $ret = false;
                }
            }
        } else {
            $ret = self::_renderNoCache($componentId, $ignoreVisible, $masterTemplate);
        }
                
        //plugins _nach_ im cache speichern ausführen
        foreach ($plugins as $p) {
            if (!$p) {
                throw new Vps_Exception("Invalid Plugin specified '$p'");
            }
            $p = new $p($componentId);
            $ret = $p->processOutput($ret);
        }

        // content-Tags ersetzen
        return self::_parseTemplate($ret, $ignoreVisible);
    }
    
    private static function _parseTemplate($ret, $ignoreVisible)
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
            
            $replace = self::_renderHasContent($c, $ignoreVisible);
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
                'pageId' => $nocacheMatches[3][$key]
            );
            $data = self::_parseComponent($c, $ignoreVisible, false, $plugins);
            if ($data['content']) {
                $ret = str_replace($search, $data['content'], $ret);   
            } else {
                $toLoad[$search] = $c;
            }
            $toLoad = array_merge($toLoad, $data['toLoad']);
        }
        return array(
            'content' => $ret,
            'toLoad' => $toLoad,
            'toLoadHasContent' => $toLoadHasContent
        );
    }
    
    private static function _renderHasContent($c, $ignoreVisible)
    {
        $componentId = $c['componentId'];
        if (self::_hasCache($componentId, $c['componentClass'])) {
            $cacheId = Vps_Component_Cache::getInstance()->getCacheIdFromComponentId($componentId, false, true);
            if (isset(self::$_componentCache[$cacheId])) {
                Vps_Benchmark::count('rendered cache (preloaded)', $componentId.' (hasContent)');
                $ret = self::$_componentCache[$cacheId];
            } else {
                if (self::_shouldBeLoaded($componentId . '-hasContent')) {
                    $ret = self::_renderHasContentNoCache($componentId, $ignoreVisible, $c['content']);
                } else {
                    $ret = false;
                }
            }
        } else {
            $ret = self::_renderHasContentNoCache($cId, $ignoreVisible, $c['content']);
        }
        return $ret;
    }
    
    private static function _renderHasContentNoCache($componentId, $ignoreVisible, $content)
    {
        $component = self::_getComponent($componentId, $ignoreVisible);
        if (!$component) {
            throw new Vps_Exception("Can't find component '$componentId'");
        }
        $cachedContent = $component->hasContent() ? $content : '';
        $cache = Vps_Component_Cache::getInstance();
        $cacheId = $cache->getCacheIdFromComponentId($componentId, false, true);
        $cache->save($cachedContent, $cacheId, array(
            'componentClass'=>$component->componentClass,
            'pageId' => $cache->getCacheIdFromComponentId($component->getPage()->componentId)
        ));
        Vps_Benchmark::count('rendered nocache', $componentId.' (hasContent)');
        return $cachedContent;
    }
    
    private static function _renderNoCache($componentId, $ignoreVisible, $masterTemplate)
    {
        $component = self::_getComponent($componentId, $ignoreVisible);
        
        $cache = Vps_Component_Cache::getInstance();
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
                $ret = self::_render($masterTemplate, $templateVars);
            } else {
                $templateVars = $component->getComponent()->getTemplateVars();
                $template = Vpc_Admin::getComponentFile($component->componentClass, 'Component', 'tpl');
                if (!$template) {
                    throw new Vps_Exception("No Template found for '$component->componentClass'");
                }
        
                if (is_null($templateVars)) {
                    throw new Vps_Exception('getTemplateVars einer Komponenten gibt null zurück. return $vars; vergessen?');
                }
        
                $ret = self::_render($template, $templateVars);
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
    
    private static function _hasCache($componentId, $componentClass)
    {
        static $cacheDisabled;
        if (is_null($cacheDisabled)) {
            $cacheDisabled = Zend_Registry::get('config')->debug->componentCache->disable;
        }
        if ($cacheDisabled) return false;
        if (!Vpc_Abstract::getSetting($componentClass, 'viewCache')) {
            return false;
        }
        return true;
    }
    
    private static function _shouldBeLoaded($componentId)
    {
        $componentId = (string)$componentId;
        if (isset(self::$_componentCache[$componentId]) ||
            in_array($componentId, self::$_loadedIds)
        )  {
            return true;
        }
        $cutId = $componentId;
        while ($cutId) {
            $pos = strrpos($cutId, '-');
            $cutId = $pos ? substr($cutId, 0, $pos) : '';
            if (in_array($cutId, self::$_loadedIds))  {
                return true;
            }
        }
        return false;
    }
    
    public static function preloadComponentCache($ids)
    {
        $parts = array();
        foreach ($ids as $key => $val) {
            if ($key) {
                if (self::_shouldBeLoaded($key)) continue;
                self::$_loadedIds[] = $key;
                $key = Vps_Component_Cache::getInstance()->getCacheIdFromComponentId($key);
                $val = Vps_Component_Cache::getInstance()->getCacheIdFromComponentId($val);
                $parts[] = "(id LIKE '{$key}%' AND page_id='$val')";
            } else {
                self::$_loadedIds[] = $val;
                $val = Vps_Component_Cache::getInstance()->getCacheIdFromComponentId($val);
                $parts[] = "page_id='$val'";
            }
        }
        if ($parts) {
            $sql = "SELECT id, content FROM cache_component WHERE " . implode(' OR ', $parts);
            Vps_Benchmark::count('preload cache', $sql);
            $rows = Zend_Registry::get('db')->query($sql)->fetchAll();
            foreach ($rows as $row) {
                self::$_componentCache[$row['id']] = $row['content'];
            }
        }
    }

    private static function _getComponent($componentId, $ignoreVisible)
    {
        return Vps_Component_Data_Root::getInstance()
            ->getComponentById($componentId, array('ignoreVisible' => $ignoreVisible));
    }

    private static function _render($template, $templateVars)
    {
        $view = new Vps_View_Component();
        $view->assign($templateVars);
        return $view->render($template);
    }

    /**
     * Finds a view script from the available directories.
     *
     * @param $name string The base name of the script.
     * @return void
     */
    protected function _script($name)
    {
        return $name;
    }
}
