<?php
class Vps_Component_Cache_Mysql extends Vps_Component_Cache
{
    protected $_models;

    public function __construct()
    {
        $this->_models = array (
            'cache' => 'Vps_Component_Cache_Mysql_Model',
            'metaModel' => 'Vps_Component_Cache_Mysql_MetaModelModel',
            'metaRow' => 'Vps_Component_Cache_Mysql_MetaRowModel',
            'metaComponent' => 'Vps_Component_Cache_Mysql_MetaComponentModel',
            'metaChained' => 'Vps_Component_Cache_Mysql_MetaChainedModel',
            'url' => 'Vps_Component_Cache_Mysql_UrlModel',
            'urlParents' => 'Vps_Component_Cache_Mysql_UrlParentsModel',
        );
    }

    /**
     * @return Vps_Model_Abstract
     */
    public function getModel($type = 'cache')
    {
        if (!isset($this->_models[$type])) return null;
        if (is_string($this->_models[$type])) {
            $this->_models[$type] = Vps_Model_Abstract::getInstance($this->_models[$type]);
        }
        return $this->_models[$type];
    }

    public function save(Vps_Component_Data $component, $content, $type = 'component', $value = '')
    {
        $settings = $component->getComponent()->getViewCacheSettings();
        if (!$settings['enabled']) $content = self::NO_CACHE;

        // MySQL
        $data = array(
            'component_id' => (string)$component->componentId,
            'db_id' => (string)$component->dbId,
            'component_class' => $component->componentClass,
            'type' => $type,
            'value' => (string)$value,
            'expire' => is_null($settings['lifetime']) ? null : time() + $settings['lifetime'],
            'deleted' => false,
            'content' => $content
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->getModel('cache')->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);

        // APC
        $cacheId = $this->_getCacheId($component->componentId, $type, $value);
        $ttl = 60*60; // Damit er nicht vollgemüllt wird, sondern alles was er wirklich oft braucht drinnen hat
        if ($settings['lifetime']) $ttl = min($ttl, $settings['lifetime']);
        apc_add($cacheId, $content, $ttl);

        return true;
    }

    public function load($componentId, $type = 'component', $value = '')
    {
        if ($componentId instanceof Vps_Component_Data) {
            $componentId = $componentId->componentId;
        }
        $cacheId = $this->_getCacheId($componentId, $type, $value);
        $content = apc_fetch($cacheId);
        if ($content === false) {
            $select = $this->getModel('cache')->select()
                ->whereEquals('component_id', $componentId)
                ->whereEquals('type', $type)
                ->whereEquals('deleted', false)
                ->whereEquals('value', $value)
                ->where(new Vps_Model_Select_Expr_Or(array(
                    new Vps_Model_Select_Expr_Higher('expire', new Vps_DateTime(time())),
                    new Vps_Model_Select_Expr_IsNull('expire'),
                )));
            $row = $this->getModel('cache')->export(Vps_Model_Db::FORMAT_ARRAY, $select);
            $content = isset($row[0]) ? $row[0]['content'] : null;
            if (isset($row[0])) {
                $ttl = min(60*60, time() - $row[0]['expire']);
                apc_add($cacheId, $content, $ttl);
            }
        }
        return $content;
    }

    protected static function _getCacheId($componentId, $type, $value)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix() . '-cc-';
        return $prefix . "$componentId/$type/$value";
    }

    // wird nur von Vps_Component_View_Renderer->saveCache() verwendet
    public function test($componentId, $type = 'component', $value = '')
    {
        return !is_null($this->load($componentId, $type, $value));
    }

    protected function _addRowWhere($wheres, $row, $metaType = Vps_Component_Cache_Meta_Abstract::META_TYPE_DEFAULT)
    {
        // Das suchen wir
        // $searchModel = model, column, value, component_id, meta_class
        // $searchModel->model = $row->model && $searchModel->value=$row->{$searchModel->column}
        $searchModel = $this->getModel('metaRow');

        $select = $searchModel->select()
            ->whereEquals('model', $this->_getModelname($row));

        // Alle Rows mit betreffenden model holen und columns suchen
        $columns = array();
        foreach ($searchModel->getRows($select) as $r) $columns[] = $r->column;
        $columns = array_unique($columns);

        // Für die Columns Values einsetzen
        $or = array();
        foreach ($columns as $column) {
            $or[] = new Vps_Model_Select_Expr_And(array(
                new Vps_Model_Select_Expr_Equal('column', $column),
                new Vps_Model_Select_Expr_Equal('value', $row->$column)
            ));
        }

        // Rausgesuchte Columns mit Values zu Select hinzufügen
        if ($or) {
            $select->where(new Vps_Model_Select_Expr_Or($or));
            foreach ($searchModel->getRows($select) as $metaRow) {
                $type = call_user_func(array($metaRow->meta_class, 'getMetaType'));
                if ($type != $metaType) continue;
                $where = call_user_func(
                    array($metaRow->meta_class, 'getDeleteWhere'),
                    $metaRow->component_id
                );
                $wheres[$metaRow->component_class][] = $where;
            }
        }
        return $wheres;
    }

    protected function _addModelWhere($wheres, $row, $dirtyColumns, $metaType = Vps_Component_Cache_Meta_Abstract::META_TYPE_DEFAULT)
    {
        $model = $this->getModel('metaModel');

        $select = $model->select()
            ->whereEquals('model', $this->_getModelname($row));

        foreach ($model->getRows($select) as $metaRow) {
            $type = call_user_func(array($metaRow->meta_class, 'getMetaType'));
            if ($type != $metaType) continue;
            $where = call_user_func(
                array($metaRow->meta_class, 'getDeleteWhere'),
                $metaRow->pattern, $row, $dirtyColumns, unserialize($metaRow->params)
            );
            if ($where) $wheres[$metaRow->component_class][] = $where;
        }
        return $wheres;
    }

    protected function _addComponentWhere($wheres)
    {
        $model = $this->getModel('metaComponent');

        // Die cache_component-Tabelle so lange in einer Schleife durchlaufe
        // bis zu den bisherigen Einträgen keine neuen mehr dazukommen
        // allIds, newIds, searchIds haben alle das gleiche Format und werden
        // verwendet, um rauszufinden, welche Ids schon bearbeitet wurden

        // Alle bisherigen wheres durchgehen und nur die nehmen, wo eine db_id gelöscht wird
        $allIds = array();
        foreach ($wheres as $class => $where) {
            $allIds[$class] = array();
            foreach ($where as $w) {
                if (isset($w['db_id'])) {
                    $ids = is_array($w['db_id']) ? $w['db_id'] : array($w['db_id']);
                    $allIds[$class] = array_merge($allIds[$class], $ids);
                }
            }
        }
        $searchIds = $allIds;
        do {
            // Tabelle durchsuchen und Ergebnisse in newIds speichern
            $newIds = array();
            foreach ($searchIds as $class => $dbIds) {
                // Einträge ohne db_id
                $select = $model->select();
                $select->whereEquals('component_class', $class);
                $select->whereEquals('db_id', '');
                foreach ($model->getRows($select) as $r) {
                    foreach ($dbIds as $dbId) {
                        $id = call_user_func(
                            array($r->meta_class, 'getDeleteDbId'), $r, $dbId
                        );
                        if (!is_null($id)) $newIds[$r->target_component_class][] = $id;
                    }
                }
                // Einträge mit db_id
                $select = $model->select();
                $select->whereEquals('db_id', $dbIds);
                foreach ($model->getRows($select) as $r) {
                    $newIds[$r->target_component_class][] = $r->target_db_id;
                }
            }
            // searchIds neu berechnen und wheres hinzufügen
            $searchIds = array();
            foreach ($newIds as $class => $ids) {
                $ids = array_unique($ids);

                // where schreiben
                $where = array();
                if (count($ids) > 0 && $ids[0]) $where = array('db_id' => $ids);
                $wheres[$class][] = $where;

                // alles was schon in allIds vorkommt nicht mehr zu searchIds hinzufügen
                if (!isset($allIds[$class])) $allIds[$class] = array();
                $diff = array_diff($ids, $allIds[$class]);
                if ($diff) {
                    $searchIds[$class] = $diff;
                    $allIds[$class] = array_unique(array_merge($allIds[$class], $searchIds[$class]));
                }
            }
        } while ($searchIds);
        return $wheres;
    }

    protected function _addChainedWhere($wheres)
    {
        $model = $this->getModel('metaChained');
        $select = $model->select()
            ->whereEquals('source_component_class', array_keys($wheres));
        foreach ($model->getRows($select) as $row) { // Alle infrage kommenden target_component_classes
            // Alle master-componentIds der target_component_class
            $scc = $row->source_component_class;
            $tcc = $row->target_component_class;
            if (!isset($wheres[$scc])) continue;
            $componentIds = $this->_getComponentIdsFromWheres(array($wheres[$scc]));
            if (!isset($wheres[$tcc])) $wheres[$tcc] = array();
            $wheres[$tcc] = array_merge(
                $wheres[$tcc],
                Vps_Component_Cache_Meta_Static_Chained::getDeleteWheres($componentIds)
            );
        }
        return $wheres;
    }

    protected function _cleanByWheres($wheres)
    {
        $select = $this->getModel('cache')->select();

        $or = array();
        //p($wheres);
        foreach ($wheres as $cClass => $where) {
            foreach ($where as $w) {
                $and = array();
                foreach ($w as $key => $val) {
                    if ($key != 'db_id') {
                        $and[] = new Vps_Model_Select_Expr_Equal($key, $val);
                    }
                }
                if (isset($w['db_id'])) {
                    $val = $w['db_id'];
                    if (is_array($val) && count($val) == 1) $val = $val[0];
                    if (!is_array($val)) $val = (string)$val;
                    if (!is_array($val) && strpos($val, '%') !== false) {
                        $and[] = new Vps_Model_Select_Expr_Like('db_id', $val);
                        $and[] = new Vps_Model_Select_Expr_Equal('component_class', $cClass);
                    } else {
                        // Hier keine componentClass-where, damit man im Pattern andere Komponente angeben kann
                        $and[] = new Vps_Model_Select_Expr_Equal('db_id', $val);
                    }
                } else {
                    $and[] = new Vps_Model_Select_Expr_Equal('component_class', $cClass);
                }
                $or[] = new Vps_Model_Select_Expr_And($and);
            }
        }
        if ($or) {
            $select->where(new Vps_Model_Select_Expr_Or($or));
            //p($select->getParts());
            foreach ($this->getModel()->export(Vps_Model_Abstract::FORMAT_ARRAY, $select) as $row) {
                $cacheId = $this->_getCacheId($row['component_id'], $row['type'], $row['value']);
                apc_delete($cacheId);
            }
            //p($this->getModel()->getRows($select)->toArray());
            $this->getModel()->updateRows(array('deleted' => true), $select);
            //d($this->getModel('metaModel')->getRows()->toArray());
            //d($this->getModel()->getRows()->toArray());
        }
    }

    public function cleanByModel(Vps_Model_Abstract $model)
    {
        $select = $this->getModel('metaModel')->select()
            ->whereEquals('model', get_class($model))
            ->whereNull('pattern');
        $componentClasses = array();
        foreach ($this->getModel('metaModel')->getRows($select) as $r) {
            if (call_user_func(array($r->meta_class, 'getMetaType')) == Vps_Component_Cache_Meta_Abstract::META_TYPE_DEFAULT) {
                $componentClasses[] = $r->component_class;
            }
        }
        $componentSelect = $this->getModel('metaComponent')->select()
            ->whereEquals('component_class', $componentClasses);
        foreach ($this->getModel('metaComponent')->getRows($componentSelect) as $r) {
            $componentClasses[] = $r->target_component_class;
        }

        $select = $this->getModel('cache')->select()
            ->whereEquals('component_class', array_unique($componentClasses));
        foreach ($this->getModel()->export(Vps_Model_Abstract::FORMAT_ARRAY, $select) as $row) {
            $cacheId = $this->_getCacheId($row['component_id'], $row['type'], $row['value']);
            apc_delete($cacheId);
        }
        $this->getModel('cache')->updateRows(
            array('deleted' => true),
            $select
        );
    }

    protected function _saveMetaModel($componentClass, $modelName, $pattern, $metaClass, $params)
    {
        $data = array(
            'model' => $modelName,
            'component_class' => $componentClass,
            'pattern' => $pattern ? $pattern : '',
            'meta_class' => $metaClass,
            'params' => serialize($params)
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->getModel('metaModel')->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    protected function _saveMetaRow(Vps_Component_Data $component, $modelName, $column, $value, $metaClass)
    {
        // TODO: checken, ob component->componentClass eh nicht schon in cache_component_meta_model mit gleichem Model steht
        $data = array(
            'model' => $modelName,
            'column' => $column,
            'value' => $value,
            'component_id' => $component->componentId,
            'component_class' => $component->componentClass,
            'meta_class' => $metaClass
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->getModel('metaRow')->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    protected function _saveMetaComponent($dbId, $componentClass, $targetDbId, $targetComponentClass, $metaClass)
    {
        $data = array(
            'db_id' => $dbId,
            'component_class' => $componentClass,
            'target_db_id' => $targetDbId,
            'target_component_class' => $targetComponentClass,
            'meta_class' => $metaClass,
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->getModel('metaComponent')->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    protected function _saveMetaChained($sourceComponentClass, $targetComponentClass)
    {
        $data = array(
            'source_component_class' => $sourceComponentClass,
            'target_component_class' => $targetComponentClass
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->getModel('metaChained')->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    public function writeBuffer()
    {
        foreach ($this->_models as $m) {
            if (is_object($m)) $m->writeBuffer();
        }
    }

    protected function _cleanUrl(Vps_Component_Data $component)
    {
        $ids[] = $component->componentId;

        $s = new Vps_Model_Select();
        $s->whereEquals('parent_page_id', $component->componentId);
        foreach ($this->getModel('urlParents')->export(Vps_Model_Abstract::FORMAT_ARRAY, $s) as $r) {
            $ids[] = $r['page_id'];
        }

        $s = new Vps_Model_Select();
        $s->whereEquals('page_id', $ids);
        foreach ($this->getModel('url')->export(Vps_Model_Abstract::FORMAT_ARRAY, $s) as $row) {
            static $prefix;
            if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix();
            $cacheId = $prefix.'url-'.$row['url'];
            apc_delete($cacheId);
        }
        $this->getModel('url')->deleteRows($s);
    }

    protected function _cleanProcessInput(Vps_Component_Data $component)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix();
        $cacheId = $prefix.'procI-'.$component->getPageOrRoot()->componentId;
        apc_delete($cacheId);
    }

    public function cleanByRow($row, $dirtyColumns = array())
    {
        parent::cleanByRow($row, $dirtyColumns);
    }
}
