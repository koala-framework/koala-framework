<?php
class Vps_Component_Output_Cache extends Vps_Component_Output_NoCache
{
    private $_cache;
    private $_toLoadHasContent = array();
    private $_toLoad = array();
    private $_toLoadPartial = array();

    /**
     * @return Vps_Component_Cache
     */
    public function getCache()
    {
        return Vps_Component_Cache::getInstance();
    }

    public function render($component, $masterTemplate = false, array $plugins = array())
    {
        // Erste Komponente vorausladen
        $this->getCache()->preload(array($component->componentId));
        // Normal rendern
        $ret = parent::render($component, $masterTemplate, $plugins);
        $this->getCache()->writeBuffer();
        return $ret;
    }

    protected function _render($componentId, $componentClass, $masterTemplate = false, array $plugins = array())
    {
        $ret = $this->_processComponent($componentId, $componentClass, $masterTemplate, $plugins);
        $ret = $this->_processComponent2($ret);
        $ret = $this->_processAfterPlugins($ret);
        return $ret;

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
            $preloadIds[] = $this->getCache()->getCacheId(
                $val['componentId'],
                Vps_Component_Cache::TYPE_HASCONTENT,
                $val['counter']
            );
        }
        foreach ($toLoadPartial as $val) {
            $preloadIds[] = $this->getCache()->getCacheId(
                $val['componentId'],
                Vps_Component_Cache::TYPE_PARTIAL,
                $val['nr']
            );
        }
        foreach ($toLoad as $val) {
            $type = $val['masterTemplate'] ? Vps_Component_Cache::TYPE_MASTER : Vps_Component_Cache::TYPE_DEFAULT;
            $preloadIds[] = $this->getCache()->getCacheId($val['componentId'], $type);
        }

        if ($preloadIds) {
            $this->getCache()->preload($preloadIds);
        }

        // Nochmal durchgehen und ersetzen
        foreach ($toLoadHasContent as $search => $val) {
            $content = $this->_renderHasContent($val['componentId'], $val['componentClass'], $val['content'], $val['counter'], $val['inverse']);
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
            foreach ($val['afterPlugins'] as $plugin) {
                $ret = $this->_executeOutputPlugin($plugin, $ret);
                $ret = $this->_parseTemplate($ret);
                $ret = $this->_processComponent2($ret);
            }
        }
        return $this->_parseTemplate($ret);
    }

    protected function _renderPartial($componentId, $componentClass, $partial, $id, $info)
    {
        $ret = false;
        $cacheId = $this->getCache()->getCacheId($componentId, Vps_Component_Cache::TYPE_PARTIAL, $id);

        if ($this->getCache()->isLoaded($cacheId)) {
            Vps_Benchmark::count('rendered partial cache', $cacheId);
            $ret = $this->getCache()->load($cacheId);
        } else if ($this->getCache()->shouldBeLoaded($cacheId)) {
            $settings = $this->_getComponent($componentId)->getComponent()->getViewCacheSettings();
            $ret = parent::_renderPartial($componentId, $componentClass, $partial, $id, $info, $settings['enabled']);
            if ($settings['enabled']) {
                $this->getCache()->save($ret, $cacheId, $componentClass, $settings['lifetime']);
                $this->_saveMeta($componentId, $cacheId, $id);
            }
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
        $ret = $this->_parseDynamic($ret, $componentClass, array('partial' => $info));
        return $ret;
    }

    protected function _renderContent($componentId, $componentClass, $masterTemplate, $afterPlugins = array())
    {
        $ret = false;
        $type = $masterTemplate ? Vps_Component_Cache::TYPE_MASTER : Vps_Component_Cache::TYPE_DEFAULT;
        $cacheId = $this->getCache()->getCacheId($componentId, $type);

        if ($this->getCache()->isLoaded($cacheId)) {
            Vps_Benchmark::count('rendered cache', $cacheId);
            $ret = $this->getCache()->load($cacheId);
        } else if ($this->getCache()->shouldBeLoaded($cacheId)) {
            $settings = $this->_getComponent($componentId)->getComponent()->getViewCacheSettings();
            $ret = parent::_renderContent($componentId, $componentClass, $masterTemplate, $afterPlugins, $settings['lifetime']);
            if ($settings['enabled']) {
                $this->getCache()->save($ret, $cacheId, $componentClass, $settings['lifetime']);
                $this->_saveMeta($componentId, $cacheId);
            }
        } else {
            $ret = "{empty: $componentId}";
            $this->_toLoad[$ret] = array(
                'componentClass' => $componentClass,
                'componentId' => $componentId,
                'masterTemplate' => $masterTemplate,
                'afterPlugins' => $afterPlugins
            );
        }
        return $ret;
    }

    protected function _renderHasContent($componentId, $componentClass, $content, $counter, $inverse)
    {
        // Komponente aus Cache holen
        $ret = false; // Falls nicht in Cache und sollte noch nicht geladen sein, kann auch false zurückgegeben werden
        $cacheId = $this->getCache()->getCacheId($componentId, Vps_Component_Cache::TYPE_HASCONTENT, $counter);

        if ($this->getCache()->isLoaded($cacheId)) { // Wurde bereits preloaded
            Vps_Benchmark::count('rendered cache', $cacheId);
            $ret = $this->getCache()->load($cacheId);
        } else if ($this->getCache()->shouldBeLoaded($cacheId)) { // Nicht in Cache, aber sollte in Cache sein -> ohne Cache holen
            $settings = $this->_getComponent($componentId)->getComponent()->getViewCacheSettings();
            $ret = parent::_renderHasContent($componentId, $componentClass, $content, $counter, $inverse, $settings['enabled']);
            if ($settings['enabled']) {
                $this->getCache()->save($ret, $cacheId, $componentClass, $settings['lifetime']);
                $this->_saveMeta($componentId, $cacheId);
            }
        } else {
            $ret = "{hasContent " . $componentId . '#' . $counter . "}";
            $this->_toLoadHasContent[$ret] = array(
                'componentId' => $componentId,
                'componentClass' => $componentClass,
                'content' => $content,
                'counter' => $counter,
                'inverse' => $inverse
            );
        }

        return $ret;
    }

    private function _saveMeta($componentId, $cacheId, $partial = false)
    {
        $component = $this->_getComponent($componentId);
        if ($partial === false) {
            $meta = $component->getComponent()->getCacheVars();
        } else {
            $meta = $component->getComponent()->getPartialCacheVars($partial);
        }
        foreach ($meta as $m) {
            if (is_string($m)) {
                $m = array(
                    'model' => $m
                );
            }
            if (is_object($m)) {
                if ($m instanceof Vps_Model_Row_Abstract) {
                    $model = $m->getModel();
                    if (get_class($model) == 'Vps_Model_Db') $model = $model->getTable();
                } else if ($m instanceof Zend_Db_Table_Row_Abstract) {
                    $model = $m->getTable();
                }
                $m = array(
                    'model' => get_class($model),
                    'id' => $m->id
                );
            }
            if (!isset($m['model'])) {
                throw new Vps_Exception('getCacheVars for ' . $component->componentClass . ' ('.$component->componentId.') must deliver model');
            }
            $model = $m['model'];
            $id = isset($m['id']) ? $m['id'] : null;
            if (isset($m['callback']) && $m['callback']) {
                $type = Vps_Component_Cache::META_CALLBACK;
                $value = $componentId;
            } else if (is_null($id)) {
                $type = Vps_Component_Cache::META_COMPONENT_CLASS;
                $value = $component->componentClass;
            } else {
                $type = Vps_Component_Cache::META_CACHE_ID;
                $value = $cacheId;
            }
            if (isset($m['componentId'])) {
                $value = $this->getCache()->getCacheId($m['componentId']);
            }
            $field = isset($m['field']) ? $m['field'] : '';
            $this->getCache()->saveMeta($model, $id, $value, $type, $field);
        }
        return $meta;
    }
}
