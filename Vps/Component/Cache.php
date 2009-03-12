<?php
class Vps_Component_Cache
{
    static private $_instance;
    private $_preloadedValues = array();
    private $_model;
    private $_metaModel;
    private $_countPreloadCalls = 0;

    const TYPE_DEFAULT = '';
    const TYPE_MASTER = 'master';
    const TYPE_HASCONTENT = 'hasContent';
    const TYPE_PARTIAL = 'partial';

    const META_CACHE_ID = 'cacheId';
    const META_CALLBACK = 'callback';
    const META_COMPONENT_CLASS = 'componentClass';

    const CLEANING_MODE_META = 'default';
    const CLEANING_MODE_COMPONENT_CLASS = 'componentClass';
    const CLEANING_MODE_ID = 'id';
    const CLEANING_MODE_ALL = 'all';

    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function getMetaModel()
    {
        if (!$this->_metaModel) {
            $this->setMetaModel(new Vps_Component_Cache_MetaModel());
        }
        return $this->_metaModel;
    }

    public function setMetaModel(Vps_Component_Cache_MetaModel $model)
    {
        $this->_metaModel = $model;
    }

    public function getModel()
    {
        if (!$this->_model) $this->setModel(new Vps_Component_Cache_Model());
        return $this->_model;
    }

    public function setModel(Vps_Component_Cache_Model $model)
    {
        $this->_model = $model;
    }

    public function save($content, $cacheId, $componentClass = '', $lifetime = null)
    {
        $lastModified = time();
        $expire = is_null($lifetime) ? 0 : $lastModified + $lifetime;
        $pageId = $this->_getPageIdFromComponentId($cacheId);
        $data = array(
            'id' => $cacheId,
            'page_id' => $pageId,
            'component_class' => $componentClass,
            'content' => $content,
            'last_modified' => $lastModified,
            'expire' => $expire
        );
        $options = array(
            'buffer' => true,
            'bufferSize' => 50,
            'replace' => true
        );
        $this->getModel()->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    public function getCacheId($componentId, $type = self::TYPE_DEFAULT, $number = null)
    {
        if ($type == self::TYPE_MASTER) {
            $componentId .= '-master';
        }
        if ($type == self::TYPE_HASCONTENT) {
            $componentId .= '-hasContent';
        }
        if ($type == self::TYPE_PARTIAL) {
            $componentId .= '~';
        }
        if ($type == self::TYPE_HASCONTENT || $type == self::TYPE_PARTIAL) {
            if (is_null($number)) throw new Vps_Exception('Missing $number.');
            $componentId .= $number;
        }
        return $componentId;
    }

    public function saveMeta($model, $id, $value, $type = self::META_CACHE_ID)
    {
        if ($model instanceof Vps_Model_Db) $model = $model->getTable();
        if ($model instanceof Vps_Model_Abstract) $model = $model;
        if (!is_string($model)) $model = get_class($model);
        if (is_null($id)) $id = ''; // Weil mySql ein null-value im index nicht zulässt
        $data = array(
            'model' => $model,
            'id' => $id,
            'value' => $value,
            'type' => $type
        );
        $options = array(
            'buffer' => true,
            'bufferSize' => 50,
            'replace' => true
        );
        $this->getMetaModel()->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    public function writeBuffer()
    {
        $this->getModel()->writeBuffer();
        $this->getMetaModel()->writeBuffer();
    }

    public function cleanComponentClass($componentClass)
    {
        $this->clean(self::CLEANING_MODE_COMPONENT_CLASS, $componentClass);
    }

    public function clean($mode = self::CLEANING_MODE_META, $value = null)
    {
        if ($this->isEmpty()) {
            foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
                $methods = get_class_methods($componentClass);
                if (in_array('getStaticCacheVars', $methods)) {
                    $vars = call_user_func(array($componentClass, 'getStaticCacheVars'));
                    foreach ($vars as $id => $m) {
                        $this->saveMeta($m['model'], null, $componentClass, Vps_Component_Cache::META_COMPONENT_CLASS);
                    }
                }
            }
        }

        if ($mode == Vps_Component_Cache::CLEANING_MODE_META) {

            if (!is_array($value) ||
                !array_key_exists('model', $value) ||
                !array_key_exists('id', $value) ||
                !array_key_exists('row', $value)
            ) {
                throw new Vps_Exception('$value must be an array with "model", "id" and "row"');
            }
            $select = $this->getMetaModel()->select()->where(
                new Vps_Model_Select_Expr_And(array(
                    new Vps_Model_Select_Expr_Equals('model', $value['model']),
                    new Vps_Model_Select_Expr_Or(array(
                        new Vps_Model_Select_Expr_Equals('id', ''),
                        new Vps_Model_Select_Expr_Equals('id', $value['id'])
                    ))
                ))
            );
            if ($this->getMetaModel()->getProxyModel() instanceof Vps_Model_Db) {
                $adapter = $this->getMetaModel()->getProxyModel()->getTable()->getAdapter();
                $model = $adapter->quote($value['model']);
                $id = $adapter->quote($value['id']);
                $sql = "
                    DELETE cache_component
                    FROM cache_component, cache_component_meta m
                    WHERE cache_component.id = m.value
                        AND ((m.model = $model)
                        AND ((m.id = '') OR (m.id = $id)))
                        AND m.type='cacheId'
                ";
                $this->getModel()->getProxyModel()->executeSql($sql);
                $sql = "
                    DELETE cache_component
                    FROM cache_component, cache_component_meta m
                    WHERE cache_component.component_class = m.value
                        AND ((m.model = $model)
                        AND ((m.id = '') OR (m.id = $id)))
                        AND m.type='componentClass'
                ";
                $this->getModel()->getProxyModel()->executeSql($sql);
                $select->whereEquals('type', 'callback');
            }
            foreach ($this->getMetaModel()->getRows($select) as $row) {
                if ($row->type == self::META_CACHE_ID) {
                    $this->clean(self::CLEANING_MODE_ID, $row->value);
                } else if ($row->type == self::META_CALLBACK) {
                    $component = Vps_Component_Data_Root::getInstance()
                        ->getComponentById($row->value);
                    if ($component) {
                        $component->getComponent()->onCacheCallback($value['row']);
                        Vps_Benchmark::cacheInfo("Cache: Callback for component {$component->componentId} ({$component->componentClass}) called.");
                    }
                } else if ($row->type == self::META_COMPONENT_CLASS) {
                    $this->clean(self::CLEANING_MODE_COMPONENT_CLASS, $row->value);
                }
            }
            return true;

        } else if ($mode == Vps_Component_Cache::CLEANING_MODE_COMPONENT_CLASS) {

            if (!is_string($value)) throw new Vps_Exception("value must be a component class");
            $select = $this->getModel()->select()->whereEquals('component_class', $value);
            $count = $this->getModel()->countRows($select);
            $this->getModel()->deleteRows($select);
            $this->emptyPreload();
            Vps_Benchmark::cacheInfo("Cache: $count entries for Component Class '$value' deleted.");
            return true;

        } else if ($mode == Vps_Component_Cache::CLEANING_MODE_ID) {

            if (!is_string($value)) throw new Vps_Exception("value must be an id");
            $select = $this->getModel()->select()->whereEquals('id', $value);
            $count = $this->getModel()->countRows($select);
            $this->getModel()->deleteRows($select);
            $this->emptyPreload();
            Vps_Benchmark::cacheInfo("Cache: $count entries for Component '$value' deleted.");
            return true;

        } else if ($mode==Zend_Cache::CLEANING_MODE_ALL) {

            $this->getModel()->deleteRows(array());
            $this->getMetaModel()->deleteRows(array());
            $this->emptyPreload();
            Vps_Benchmark::cacheInfo("Cache: completely deleted.");
            return true;

        }
        return false;
    }

    private function _getPageIdFromComponentId($componentId)
    {
        $pagePos = strrpos($componentId, '_');
        $componentPos = strpos($componentId, '-', $pagePos);
        $partialPos = strpos($componentId, '~', $pagePos);
        $cutPos = 0;
        if ($componentPos) $cutPos = $componentPos;
        if ($partialPos) $cutPos = $partialPos;
        if ($componentPos && $partialPos) $cutPos = min($componentPos, $partialPos);

        $ret = $componentId;
        if ($cutPos > $pagePos) {
            $ret = substr($componentId, 0, $cutPos);
        }
        return $ret;
    }

    public function emptyPreload()
    {
        $this->_preloadedValues = array();
        $this->_countPreloadCalls = 0;
    }

    public function load($id)
    {
        if (!$this->isLoaded($id)) {
            $this->preload(array($id));
        }
        return $this->_preloadedValues[$id];
    }

    public function preload($ids)
    {
        $this->_countPreloadCalls++;
        $this->_preloadedValues += $this->_preload($ids);
    }

    public function isEmpty()
    {
        return $this->getModel()->countRows() == 0;
    }

    protected function _preload($ids)
    {
        $or = array();
        $values = array();
        foreach ($ids as $id) {
            $pageId = $this->_getPageIdFromComponentId($id);
            $or[] = new Vps_Model_Select_Expr_And(array(
                new Vps_Model_Select_Expr_Like('id', $id . '%'),
                new Vps_Model_Select_Expr_Equals('page_id', $pageId)
            ));
            $values[$id] = null;
        }
        $select = $this->getModel()->select()->where(
            new Vps_Model_Select_Expr_Or($or)
        );
        if ($values) {
            Vps_Benchmark::count('preload cache', implode(', ', $ids));
            $rows = $this->getModel()->export(Vps_Model_Db::FORMAT_ARRAY, $select);
            foreach ($rows as $row) {
                if ($row['expire'] == 0 || $row['expire'] > time()) {
                    $values[(string)$row['id']] = $row['content'];
                }
            }
        }
        return $values;
    }

    public function shouldBeLoaded($id)
    {
        return array_key_exists($id, $this->_preloadedValues);
    }

    public function isLoaded($id)
    {
        return isset($this->_preloadedValues[$id]);
    }

    public function countPreloadCalls()
    {
        return $this->_countPreloadCalls;
    }
}
