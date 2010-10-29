<?php
class Vps_Component_Cache_Mysql extends Vps_Component_Cache
{
    protected $_models;

    public function __construct()
    {
        $this->_models = array (
            'cache' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_Model'),
            'preload' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_PreloadModel'),
            'metaModel' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_MetaModelModel'),
            'metaRow' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_MetaRowModel'),
            'metaComponent' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_MetaComponentModel'),
            'metaChained' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_MetaChainedModel'),
        );
    }

    /**
     * @return Vps_Model_Abstract
     */
    public function getModel($type = 'cache')
    {
        return isset($this->_models[$type]) ? $this->_models[$type] : null;
    }

    public function save(Vps_Component_Data $component, $content, $type = 'component', $value = '')
    {
        $settings = $component->getComponent()->getViewCacheSettings();

        $page = $component;
        while ($page && !$page->isPage) $page = $page->parent;
        $expire = is_null($settings['lifetime']) ? null : time() + $settings['lifetime'];

        $data = array(
            'component_id' => $component->componentId,
            'page_id' => $page ? $page->componentId : null,
            'db_id' => $component->dbId,
            'component_class' => $component->componentClass,
            'type' => $type,
            'value' => $value,
            'expire' => $expire,
            'deleted' => false,
            'content' => $content
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['cache']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
        return true;
    }

    public function load(Vps_Component_Data $component, $type = 'component', $value = '')
    {
        $select = $this->_models['cache']->select()
            ->whereEquals('component_id', $component->componentId)
            ->whereEquals('type', $type)
            ->whereEquals('deleted', false)
            ->whereEquals('value', $value);
        $row = $this->_models['cache']->export(Vps_Model_Db::FORMAT_ARRAY, $select);
        if ($row) return $row[0]['content'];
        return null;
    }

    public function preload(Vps_Component_Data $component)
    {
        $page = $component;
        while ($page && !$page->isPage) $page = $page->parent;
        $or = array();

        // Alles von eigener Page
        if ($page) {
            $or[] = new Vps_Model_Select_Expr_Equal('page_id', $page->componentId);
        } else {
            $or[] = new Vps_Model_Select_Expr_IsNull('page_id');
        }

        // PreloadIds der Page
        $preloadSelect = $this->getModel('preload')->select();
        if ($page) {
            $preloadSelect->whereEquals('page_id', $page->componentId);
        } else {
            $preloadSelect->whereNull('page_id');
        }
        $preloadIds = array();
        foreach ($this->getModel('preload')->export(Vps_Model_Db::FORMAT_ARRAY, $preloadSelect) as $preload) {
            $preloadIds[$preload['preload_type']][] = $preload['preload_component_id'];
        }
        if ($preloadIds) {
            foreach ($preloadIds as $type => $ids) {
                $or[] = new Vps_Model_Select_Expr_And(array(
                    new Vps_Model_Select_Expr_Equal('type', $type),
                    new Vps_Model_Select_Expr_Equal('component_id', $ids)
                ));
            }
        }

        $select = $this->getModel()->select()->where(
            new Vps_Model_Select_Expr_And(array(
                new Vps_Model_Select_Expr_Equal('deleted', 0),
                new Vps_Model_Select_Expr_Or(array(
                    new Vps_Model_Select_Expr_IsNull('expire'),
                    new Vps_Model_Select_Expr_LowerEqual('expire', time())
                )),
                new Vps_Model_Select_Expr_Or($or)
            ))
        );

        $ret = array();
        foreach ($this->getModel()->export(Vps_Model_Db::FORMAT_ARRAY, $select) as $row) {
            $ret[$row['type']][(string)$row['component_id']][(string)$row['value']] = $row['content'];
        }
        return $ret;
    }

    public function savePreload($pageId, $preloadComponentId, $preloadType)
    {
        $data = array(
            'page_id' => $pageId,
            'preload_component_id' => $preloadComponentId,
            'preload_type' => $preloadType
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['preload']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
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

    protected function _addModelWhere($wheres, $row, $metaType = Vps_Component_Cache_Meta_Abstract::META_TYPE_DEFAULT)
    {
        $model = $this->getModel('metaModel');

        $select = $model->select()
            ->whereEquals('model', $this->_getModelname($row));

        foreach ($model->getRows($select) as $metaRow) {
            $type = call_user_func(array($metaRow->meta_class, 'getMetaType'));
            if ($type != $metaType) continue;
            $where = call_user_func(
                array($metaRow->meta_class, 'getDeleteWhere'),
                $metaRow->pattern, $row
            );
            $wheres[$metaRow->component_class][] = $where;
        }
        return $wheres;
    }

    protected function _addComponentWhere($wheres)
    {
        // $model: component_id component_class target_component_id target_component_class
        // So lange $model nach component_id durchsuchen und in ergebnis
        // $model->target_component_id reingeben bis nichts mehr neues kommt
        $ids = $this->_getComponentIdsFromWheres($wheres);

        $model = $this->getModel('metaComponent');
        $ret = array();
        do {
            $select = $model->select();
            $select->whereEquals('component_id', $ids);
            $ids2 = array();
            foreach ($model->getRows($select) as $r) {
                $ids2[] = $r->target_component_id;
                $ret[$r->target_component_class][] = $r->target_component_id;
            }
            $ids2 = array_diff($ids2, $ids);
            $ids = array_unique(array_merge($ids, $ids2));
        } while ($ids2);
        foreach ($ret as $componentClass => $componentIds) {
            foreach (array_unique($componentIds) as $componentId) {
                $wheres[$componentClass][] = array(
                    'db_id' => $componentId
                );
            }
        }
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
        $componentClasses = array_unique($componentClasses);
        $this->getModel('cache')->updateRows(
            array('deleted' => true),
            $this->getModel('cache')->select()->whereEquals('component_class', $componentClasses)
        );
    }

    protected function _saveMetaModel($componentClass, $modelName, $pattern, $metaClass)
    {
        $data = array(
            'model' => $modelName,
            'component_class' => $componentClass,
            'pattern' => $pattern,
            'meta_class' => $metaClass,
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['metaModel']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
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
        $this->_models['metaRow']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    protected function _saveMetaComponent(Vps_Component_Data $component, Vps_Component_Data $target)
    {
        $data = array(
            'component_id' => $component->componentId,
            'component_class' => $component->componentClass,
            'target_component_id' => $target->componentId,
            'target_component_class' => $target->componentClass
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['metaComponent']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
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
        $this->_models['metaChained']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    public function writeBuffer()
    {
        foreach ($this->_models as $m) {
            $m->writeBuffer();
        }
    }
}
