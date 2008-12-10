<?php
class Vps_Component_Output_Cache extends Vps_Component_Output_NoCache
{
    private $_cache;
    private $_toLoadHasContent = array();
    private $_toLoad = array();

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
        $this->getCache()->preload(array($componentId => $pageId));
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
        foreach ($toLoadHasContent as $val) {
            $componentId = $this->getCache()->getCacheIdFromComponentId($val['componentId'], false, true);
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
            $content = $this->_renderHasContent($val['componentId'], $val['componentClass'], $val['content']);
            $childRenderData = $this->_parseTemplate($content, true);
            $replace = $this->_processComponent2($childRenderData);
            $ret = str_replace($search, $replace, $ret);
        }
        foreach ($toLoad as $search => $val) {
            $replace = $this->_render($val['componentId'], $val['componentClass'], $val['masterTemplate']);
            $ret = str_replace($search, $replace, $ret);
        }
        return $ret;
    }

    protected function _renderContent($componentId, $componentClass, $masterTemplate)
    {
        if (!Vpc_Abstract::getSetting($componentClass, 'viewCache')) {
            return parent::_renderContent($componentId, $componentClass, $masterTemplate);
        }
        $ret = false;
        $cacheId = $this->getCache()->getCacheIdFromComponentId($componentId, $masterTemplate);

        if ($this->getCache()->isLoaded($cacheId)) {
            Vps_Benchmark::count('rendered cache (preloaded)', $componentId.($masterTemplate?' (master)':''));
            $ret = $this->getCache()->load($cacheId);
        } else if ($this->getCache()->shouldBeLoaded($cacheId)) {
            $ret = parent::_renderContent($componentId, $componentClass, $masterTemplate);
            $tags = array(
                'componentClass' => $componentClass,
                'pageId' => $this->getCache()->getCacheIdFromComponentId($this->_getPageIdFromComponentId($componentId))
            );
            $lifetime = $this->_getComponent($componentId)->getComponent()->getViewCacheLifetime();
            $this->getCache()->save($ret, $cacheId, $tags, $lifetime);
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

    protected function _renderHasContent($componentId, $componentClass, $content)
    {
        // Wenn Komponente keinen View Cache hat, ohne Cache ausgeben
        if (!Vpc_Abstract::getSetting($componentClass, 'viewCache')) {
            return parent::_renderHasContent($componentId, $componentClass, $content);
        }

        // Komponente aus Cache holen
        $ret = false; // Falls nicht in Cache und sollte noch nicht geladen sein, kann auch false zurückgegeben werden
        $cacheId = $this->getCache()->getCacheIdFromComponentId($componentId, false, true);

        if ($this->getCache()->isLoaded($cacheId)) { // Wurde bereits preloaded
            Vps_Benchmark::count('rendered cache (preloaded)', $componentId.' (hasContent)');
            $ret = $this->getCache()->load($cacheId);
        } else if ($this->getCache()->shouldBeLoaded($cacheId)) { // Nicht in Cache, aber sollte in Cache sein -> ohne Cache holen
            $ret = parent::_renderHasContent($componentId, $componentClass, $content);
            $lifetime = $this->_getComponent($componentId)->getComponent()->getViewCacheLifetime();
            $this->getCache()->save($ret, $cacheId, array(
                'componentClass' => $componentClass,
                'pageId' => $this->getCache()->getCacheIdFromComponentId($this->_getPageIdFromComponentId($componentId))
            ), $lifetime);
        } else {
            $ret = "{hasContent " . $componentId . '#' . uniqid() . "}";
            $this->_toLoadHasContent[$ret] = array(
                'componentId' => $componentId,
                'componentClass' => $componentClass,
                'content' => $content
            );
        }

        return $ret;
    }

    private function _getPageIdFromComponentId($componentId)
    {
        $pos = strrpos($componentId, '_');
        if (!$pos) $pos = strlen($componentId);
        return substr($componentId, 0, $pos);
    }
}
