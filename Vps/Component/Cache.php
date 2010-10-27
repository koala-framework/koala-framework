<?php
class Vps_Component_Cache
{
    static private $_instance;
    private $_preloadedValues = array();
    private $_model;
    private $_metaModel;
    private $_fieldsModel;
    private $_countPreloadCalls = 0;
    private $_fieldCache;

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

    public static function clearInstance()
    {
        self::$_instance = null;
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

    public function getFieldsModel()
    {
        if (!$this->_fieldsModel) {
            $this->setFieldsModel(new Vps_Component_Cache_FieldsModel());
        }
        return $this->_fieldsModel;
    }

    public function setFieldsModel(Vps_Component_Cache_FieldsModel $model)
    {
        $this->_fieldsModel = $model;
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

    public function saveCacheVars($component, $meta, $cacheId = null)
    {
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
                $value = $component->componentId;
            } else if (is_null($id)) {
                $type = Vps_Component_Cache::META_COMPONENT_CLASS;
                $value = $component->componentClass;
            } else {
                $type = Vps_Component_Cache::META_CACHE_ID;
                $value = $cacheId;
            }
            if (isset($m['componentId'])) {
                $value = $this->getCacheId($m['componentId']);
            }
            $field = isset($m['field']) ? $m['field'] : '';
            $this->saveMeta($model, $id, $value, $type, $field);
        }
        return $meta;
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

    public function saveMeta($model, $id, $value, $type = self::META_CACHE_ID, $field = '')
    {
        if (is_object($model) && get_class($model) == 'Vps_Model_Db') $model = $model->getTable();
        if ($model instanceof Vps_Model_Abstract) $model = $model;
        if (!$type) $type = self::META_CACHE_ID;
        if (!is_string($model)) $model = get_class($model);
        if (is_null($id)) $id = ''; // Weil mySql ein null-value im index nicht zulässt
        if ($id == '') $field = '';
        $data = array(
            'model' => $model,
            'id' => $id,
            'field' => $field,
            'value' => $value,
            'type' => $type
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->getMetaModel()->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
        if ($id != '') {
            $fields = $this->_getFields($model);
            if (!$fields) $fields = array();
            if (!in_array($field, $fields)) $this->_addField($model, $field);
        }
    }

    private function _addField($model, $field)
    {
        $this->getFieldsModel()->import(
            Vps_Model_Abstract::FORMAT_ARRAY,
            array(array('model' => $model, 'field' => $field)),
            array('replace' => true)
        );
        $this->_fieldCache[$model][] = $field;
    }

    private function _getFields($modelname)
    {
        if (!$this->_fieldCache) {
            foreach ($this->getFieldsModel()->export(Vps_Model_Abstract::FORMAT_ARRAY) as $row) {
                $this->_fieldCache[$row['model']][] = $row['field'];
            }
        }
        if (!isset($this->_fieldCache[$modelname])) return null;
        return $this->_fieldCache[$modelname];
    }

    public function writeBuffer()
    {
        if ($this->_model) $this->_model->writeBuffer();
        if ($this->_metaModel) $this->_metaModel->writeBuffer();
    }

    public function cleanComponentClass($componentClass)
    {
        $this->clean(self::CLEANING_MODE_COMPONENT_CLASS, $componentClass);
    }

    public static function refreshStaticCache()
    {
        foreach (Vpc_Abstract::getComponentClasses() as $componentClass) {
            $cls = strpos($componentClass, '.') ? substr($componentClass, 0, strpos($componentClass, '.')) : $componentClass;
            $vars = call_user_func(array($cls, 'getStaticCacheVars'), $componentClass);
            foreach ($vars as $id => $model) {
                if (is_array($model)) {
                    if (!isset($model['model'])) {
                        throw new Vps_Exception("$cls::getStaticCacheVars doesn't return a model");
                    }
                    $model = $model['model'];
                }
                Vps_Component_Cache::getInstance()->saveMeta($model, null, $componentClass, Vps_Component_Cache::META_COMPONENT_CLASS);
            }
        }
        Vps_Component_Cache::getInstance()->getMetaModel()->writeBuffer();
    }

    public function clean($mode = self::CLEANING_MODE_META, $value = null)
    {
        // ignore_user_abort, damit beim clientseitigen Unterbrechen vom Cache löschen (zB. weil
        // es zu lange dauert) es trotzdem fertig ausgeführt wird, damit eventuelle Fehler oder
        // slow queries in das fehlerlog kommen
        ignore_user_abort(true);
        if ($mode == Vps_Component_Cache::CLEANING_MODE_META) {
            $id = 'null';
            if ($value instanceof Vps_Model_Interface) {
                $model = $value;
                if ($model instanceof Zend_Db_Table_Abstract || get_class($model) == 'Vps_Model_Db') {
                    $model = $model->getTable();
                }
                $modelname = get_class($model);
                $select = $this->getMetaModel()->select()->where(
                    new Vps_Model_Select_Expr_Equals('model', $modelname)
                );
                $sqlInsert = '';
            } else {
                $row = $value;
                if (!$row instanceof Vps_Model_Row_Abstract && !$row instanceof Zend_Db_Table_Row_Abstract) {
                    throw new Vps_Exception('$value must be instance of Vps_Model_Row_Abstract or Zend_Db_Table_Row_Abstract');
                }
                if ($row instanceof Zend_Db_Table_Row_Abstract) {
                    $model = $row->getTable();
                    $primaryKey = current($model->info('primary'));
                } else {
                    $model = $row->getModel();
                    $primaryKey = $model->getPrimaryKey();
                    if (get_class($model) == 'Vps_Model_Db') $model = $model->getTable();
                }
                $modelname = get_class($model);
                $fields = $this->_getFields($modelname);
                if (!$fields) $fields = array('');
                $or = array(new Vps_Model_Select_Expr_Equals('id', ''));
                $sqlOr = '';
                foreach ($fields as $field) {
                    if ($field != '') {
                        $id = $row->$field;
                    } else {
                        $id = is_array($primaryKey) ? null : $row->$primaryKey;
                    }
                    $sqlOr .= "OR (m.id='$id' AND m.field='$field')";
                    $or[] = new Vps_Model_Select_Expr_And(array(
                        new Vps_Model_Select_Expr_Equals('id', $id),
                        new Vps_Model_Select_Expr_Equals('field', $field)
                    ));
                }
                $sqlInsert = "AND (m.id = '' $sqlOr)";
                $select = $this->getMetaModel()->select()->where(
                    new Vps_Model_Select_Expr_And(array(
                        new Vps_Model_Select_Expr_Equals('model', $modelname),
                        new Vps_Model_Select_Expr_Or($or)
                    ))
                );
            }

            if ($this->getMetaModel()->getProxyModel() instanceof Vps_Model_Db) {
                $sql = "
                    UPDATE cache_component c, cache_component_meta m
                    SET c.DELETED = 1
                    WHERE c.id = m.value
                        AND m.model = '$modelname'
                        $sqlInsert
                        AND m.type='cacheId'
                ";
                $this->getModel()->getProxyModel()->executeSql($sql);
                $sql = "
                    UPDATE cache_component c, cache_component_meta m
                    SET DELETED = 1
                    WHERE c.component_class = m.value
                        AND m.model = '$modelname'
                        $sqlInsert
                        AND m.type='componentClass'
                ";
                $this->getModel()->getProxyModel()->executeSql($sql);
                Vps_Benchmark::cacheInfo("Cache: cleared $modelname with $id");
                $select->whereEquals('type', 'callback');
            }

            foreach ($this->getMetaModel()->getRows($select) as $metaRow) {
                if ($metaRow->type == self::META_CACHE_ID) {
                    $this->clean(self::CLEANING_MODE_ID, $metaRow->value);
                } else if ($metaRow->type == self::META_CALLBACK) {
                    $component = Vps_Component_Data_Root::getInstance()
                        ->getComponentById($metaRow->value, array('ignoreVisible' => true));
                    if ($component) {
                        $component->getComponent()->onCacheCallback($value);
                        Vps_Benchmark::cacheInfo("Cache: Callback for component {$component->componentId} ({$component->componentClass}) called.");
                    } else {
                        Vps_Benchmark::cacheInfo("Cache-ERROR: Callback for component {$metaRow->value} not found.");
                    }
                } else if ($metaRow->type == self::META_COMPONENT_CLASS) {
                    $this->clean(self::CLEANING_MODE_COMPONENT_CLASS, $metaRow->value);
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
        $this->_fieldCache = null;
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

        // frueher blieb da in den preloadedValues teilweise null drinstehen
        // obwohl ueber das _preload() was daherkam, wurde dann aber nicht verwendet.
        // array_merge nicht nehmen, das setzt werte die dann zu rekursiven
        // endlos-aufrufen führen
        foreach ($this->_preload($ids) as $k => $v) {
            if ($v || !array_key_exists($k, $this->_preloadedValues)) {
                $this->_preloadedValues[$k] = $v;
            }
        }
    }

    protected function _preload($ids)
    {
        if (!$this->_model || $this->getModel()->getProxyModel() instanceof Vps_Model_Db) {
            $db = Vps_Registry::get('db');

            $or = array();
            $values = array();
            foreach ($ids as $id) {
                $pageId = $this->_getPageIdFromComponentId($id);
                $or[] = 'id LIKE '.$db->quote("$id%").' AND page_id='.$db->quote($pageId);
                $values[$id] = null;
            }
            if ($values) {
                $sql = "SELECT id, content, expire FROM cache_component WHERE (".implode(' OR ', $or) . ') AND deleted=0';
                Vps_Benchmark::count('preload cache', implode(', ', $ids));
                $rows = $db->query($sql)->fetchAll();
                foreach ($rows as $row) {
                    if ($row['expire'] == 0 || $row['expire'] > time()) {
                        $values[(string)$row['id']] = $row['content'];
                    }
                }
            }
            return $values;
        } else {
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
