<?php
class Vps_Component_Output_Cache extends Vps_Component_Output_NoCache
{
    private $_cache;
    private $_toLoadHasContent = array();
    private $_toLoad = array();
    private $_toLoadPartial = array();

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

    public function render($component, $masterTemplate = false, array $plugins = array())
    {
        // Erste Komponente vorausladen
        $componentId = $this->getCache()->getCacheIdFromComponentId($component->componentId, $masterTemplate);
        $pageId = $component->getPage() ? $component->getPage()->componentId : '';
        $pageId = $this->getCache()->getCacheIdFromComponentId($pageId);
        $result = $this->getCache()->preload(array($componentId => $pageId));
        if (!$result) {
            $meta = array();
            foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
                $methods = get_class_methods($componentClass);
                if (in_array('getStaticCacheVars', $methods)) {
                    $vars = call_user_func(array($componentClass, 'getStaticCacheVars'));
                    foreach ($vars as $id => $m) {
                        if ($m['model'] instanceof Vps_Model_Db) $m['model'] = get_class($m['model']->getTable());
                        if ($m['model'] instanceof Vps_Model_Abstract) $m['model'] = get_class($m['model']);
                        $vars[$id]['model'] = $m['model'];
                        $vars[$id]['component_class'] = $componentClass;
                        $vars[$id]['cache_id'] = null;
                        $vars[$id]['callback'] = null;
                    }
                    $meta = array_merge($meta, $vars);
                }
            }
            $this->getCache()->saveMeta($meta);
        }
        // Normal rendern
        return parent::render($component, $masterTemplate, $plugins);
    }

    protected function _render($componentId, $componentClass, $masterTemplate = false, array $plugins = array())
    {
        $ret = $this->_processComponent($componentId, $componentClass, $masterTemplate, $plugins);
        return $this->_processComponent2($ret);
    }

    protected function _processComponent2($ret)
    {
        // Übergebene Ids preloaden
        $preloadIds = array();
        $toLoadHasContent = $this->_toLoadHasContent;
        $this->_toLoadHasContent = array();
        $toLoad = $this->_toLoad;
        $this->_toLoad = array();
        $toLoadPartial = $this->_toLoadPartial;
        $this->_toLoadPartial = array();
        foreach ($toLoadHasContent as $val) {
            $componentId = $this->getCache()->getCacheIdFromComponentId($val['componentId'], false, true) . $val['counter'];
            $pageId = $this->getCache()->getCacheIdFromComponentId($this->_getPageIdFromComponentId($val['componentId']));
            $preloadIds[$componentId] = $pageId;
        }
        foreach ($toLoadPartial as $val) {
            $componentId = $this->getCache()->getCacheIdFromComponentId($val['componentId'], false, false, $val['nr']);
            $pageId = $this->getCache()->getCacheIdFromComponentId($this->_getPageIdFromComponentId($val['componentId']));
            $preloadIds[$componentId] = $pageId;
        }
        foreach ($toLoad as $val) {
            $componentId = $this->getCache()->getCacheIdFromComponentId($val['componentId'], $val['masterTemplate']);
            $pageId = $this->getCache()->getCacheIdFromComponentId($this->_getPageIdFromComponentId($val['componentId']));
            $preloadIds[$componentId] = $pageId;
        }
        $this->getCache()->preload($preloadIds);

        // Nochmal durchgehen und ersetzen
        foreach ($toLoadHasContent as $search => $val) {
            $content = $this->_renderHasContent($val['componentId'], $val['componentClass'], $val['content'], $val['counter']);
            $childRenderData = $this->_parseTemplate($content);
            $replace = $this->_processComponent2($childRenderData);
            $ret = str_replace($search, $replace, $ret);
        }
        foreach ($toLoadPartial as $search => $val) {
            $content = $this->_renderPartial($val['componentId'], $val['componentClass'], $val['partial'], $val['nr'], $val['info']);
            $childRenderData = $this->_parseTemplate($content);
            $replace = $this->_processComponent2($childRenderData);
            $ret = str_replace($search, $replace, $ret);
        }
        foreach ($toLoad as $search => $val) {
            $replace = $this->_render($val['componentId'], $val['componentClass'], $val['masterTemplate']);
            $ret = str_replace($search, $replace, $ret);
        }
        return $ret;
    }

    protected function _renderPartial($componentId, $componentClass, $partial, $id, $info)
    {
        if (!$this->_hasViewCache($componentClass)) {
            return parent::_renderPartial($componentId, $componentClass, $partial, $id, $info);
        }
        $ret = false;
        $cacheId = $this->getCache()->getCacheIdFromComponentId($componentId, false, false, $id);

        if ($this->getCache()->isLoaded($cacheId)) {
            Vps_Benchmark::count('rendered partial cache', $componentId . '~' . $id);
            $ret = $this->getCache()->load($cacheId);
        } else if ($this->getCache()->shouldBeLoaded($cacheId)) {
            $ret = parent::_renderPartial($componentId, $componentClass, $partial, $id, $info);
            $tags = array(
                'componentClass' => $componentClass,
                'pageId' => $this->getCache()->getCacheIdFromComponentId($this->_getPageIdFromComponentId($componentId))
            );
            $lifetime = $this->_getComponent($componentId)->getComponent()->getViewCacheLifetime();
            $this->getCache()->save($ret, $cacheId, $tags, $lifetime);
            $this->getCache()->saveMeta($this->_getMeta($componentId, $cacheId, $id));
        } else {
            $ret = "{partial: $componentId $id}";
            $this->_toLoadPartial[$ret] = array(
                'componentClass' => $componentClass,
                'componentId' => $componentId,
                'partial' => $partial,
                'nr' => $id,
                'info' => $info
            );
        }
        return $ret;
    }

    protected function _renderContent($componentId, $componentClass, $masterTemplate)
    {
        if (!$this->_hasViewCache($componentClass)) {
            return parent::_renderContent($componentId, $componentClass, $masterTemplate);
        }
        $ret = false;
        $cacheId = $this->getCache()->getCacheIdFromComponentId($componentId, $masterTemplate);

        if ($this->getCache()->isLoaded($cacheId)) {
            Vps_Benchmark::count('rendered cache', $componentId.($masterTemplate?' (master)':''));
            $ret = $this->getCache()->load($cacheId);
        } else if ($this->getCache()->shouldBeLoaded($cacheId)) {
            $ret = parent::_renderContent($componentId, $componentClass, $masterTemplate);
            $tags = array(
                'componentClass' => $componentClass,
                'pageId' => $this->getCache()->getCacheIdFromComponentId($this->_getPageIdFromComponentId($componentId))
            );
            $lifetime = $this->_getComponent($componentId)->getComponent()->getViewCacheLifetime();
            $this->getCache()->save($ret, $cacheId, $tags, $lifetime);
            $this->getCache()->saveMeta($this->_getMeta($componentId, $cacheId));
        } else {
            $ret = "{empty: $componentId}";
            $this->_toLoad[$ret] = array(
                'componentClass' => $componentClass,
                'componentId' => $componentId,
                'masterTemplate' => $masterTemplate
            );
        }
        return $ret;
    }

    protected function _renderHasContent($componentId, $componentClass, $content, $counter)
    {
        // Wenn Komponente keinen View Cache hat, ohne Cache ausgeben
        if (!$this->_hasViewCache($componentClass)) {
            return parent::_renderHasContent($componentId, $componentClass, $content, $counter);
        }

        // Komponente aus Cache holen
        $ret = false; // Falls nicht in Cache und sollte noch nicht geladen sein, kann auch false zurückgegeben werden
        $cacheId = $this->getCache()->getCacheIdFromComponentId($componentId, false, true) . $counter;

        if ($this->getCache()->isLoaded($cacheId)) { // Wurde bereits preloaded
            Vps_Benchmark::count('rendered cache', $componentId.' (hasContent)');
            $ret = $this->getCache()->load($cacheId);
        } else if ($this->getCache()->shouldBeLoaded($cacheId)) { // Nicht in Cache, aber sollte in Cache sein -> ohne Cache holen
            $ret = parent::_renderHasContent($componentId, $componentClass, $content, $counter);
            $lifetime = $this->_getComponent($componentId)->getComponent()->getViewCacheLifetime();
            $tags = array(
                'componentClass' => $componentClass,
                'pageId' => $this->getCache()->getCacheIdFromComponentId($this->_getPageIdFromComponentId($componentId))
            );
            $this->getCache()->save($ret, $cacheId, $tags, $lifetime);
            $this->getCache()->saveMeta($this->_getMeta($componentId, $cacheId));
        } else {
            $ret = "{hasContent " . $componentId . '#' . $counter . "}";
            $this->_toLoadHasContent[$ret] = array(
                'componentId' => $componentId,
                'componentClass' => $componentClass,
                'content' => $content,
                'counter' => $counter
            );
        }

        return $ret;
    }

    private function _getMeta($componentId, $cacheId, $partial = false)
    {
        $component = $this->_getComponent($componentId);
        if ($partial === false) {
            $meta = $component->getComponent()->getCacheVars();
        } else {
            $meta = $component->getComponent()->getPartialCacheVars($partial);
        }
        foreach ($meta as $id => &$m) {
            if (!isset($m['model'])) throw new Vps_Exception('getCacheVars must deliver model');
            if ($m['model'] instanceof Vps_Model_Db) $m['model'] = get_class($m['model']->getTable());
            if ($m['model'] instanceof Vps_Model_Abstract) $m['model'] = get_class($m['model']);
            $meta[$id]['model'] = $m['model'];
            if (!isset($m['callback'])) {
                $meta[$id]['callback'] = null;
                $meta[$id]['cache_id'] = $cacheId;
            } else {
                $meta[$id]['callback'] = $componentId;
                $meta[$id]['cache_id'] = null;
            }
            $meta[$id]['component_class'] = null;
        }
        return $meta;
    }

    private function _getPageIdFromComponentId($componentId)
    {
        $pos = strrpos($componentId, '_');
        if (!$pos) $pos = strpos($componentId, '-');
        if (!$pos) $pos = strlen($componentId);
        return substr($componentId, 0, $pos);
    }
}
