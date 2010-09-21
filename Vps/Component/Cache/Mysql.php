<?php
class Vps_Component_Cache_Mysql extends Vps_Component_Cache
{
    protected $_models;
    const CLEAN_DEFAULT = 'default';

    public function __construct()
    {
        $this->_models = array (
            'cache' => new Vps_Component_Cache_Mysql_Model(),
            'preload' => new Vps_Component_Cache_Mysql_PreloadModel(),
            'metaModel' => new Vps_Component_Cache_Mysql_MetaModelModel(),
            'metaRow' => new Vps_Component_Cache_Mysql_MetaRowModel(),
            'metaComponent' => new Vps_Component_Cache_Mysql_MetaComponentModel(),
            'metaChained' => new Vps_Component_Cache_Mysql_MetaChainedModel()
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
        $expire = is_null($settings['lifetime']) ? 0 : time() + $settings['lifetime'];

        $data = array(
            'component_id' => $component->componentId,
            'page_id' => $page ? $page->componentId : null,
            'component_class' => $component->componentClass,
            'type' => $type,
            'value' => $value,
            'expire' => $expire,
            'deleted' => 0,
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
            ->whereEquals('deleted', 0)
            ->whereEquals('value', $value);
        $row = $this->_models['cache']->export(Vps_Model_Db::FORMAT_ARRAY, $select);
        if ($row) return $row[0]['content'];
        return null;
    }

    public function preload(Vps_Component_Data $component)
    {
        $ret = array();

        $select = $this->_models['cache']->select();
        $select->whereEquals('deleted', 0);
        $preloadSelect = $this->_models['preload']->select();
        $or = array();
        while ($component && !$component->isPage) $component = $component->parent;
        if ($component) {
            $or[] = new Vps_Model_Select_Expr_Equals('page_id', $component->componentId);
            $preloadSelect->whereEquals('page_id', $component->componentId);
        } else {
            $or[] = new Vps_Model_Select_Expr_IsNull('page_id');
            $preloadSelect->whereNull('page_id');
        }
        $preloadIds = array();
        foreach ($this->_models['preload']->export(Vps_Model_Db::FORMAT_ARRAY, $preloadSelect) as $preload) {
            $preloadIds[] = $preload['preload_id'];
        }
        if ($preloadIds) {
            $or[] = new Vps_Model_Select_Expr_Equals('component_id', $preloadIds);
        }

        while ($component) {
            $component = $component->parent;
            if ($component && $component->isPage) {
                $or[] = new Vps_Model_Select_Expr_Equals('page_id', $component->componentId);
                /*
                $or[] = new Vps_Model_Select_Expr_And(array(
                    new Vps_Model_Select_Expr_Equals('page_id', $component->componentId),
                    new Vps_Model_Select_Expr_Equals('type', 'box')
                ));
                */
            }
        }
        $or[] = new Vps_Model_Select_Expr_IsNull('page_id');
        $select->where(new Vps_Model_Select_Expr_Or($or));
        foreach ($this->_models['cache']->export(Vps_Model_Db::FORMAT_ARRAY, $select) as $row) {
            if ($row['expire'] == 0 || $row['expire'] > time()) {
                $ret[$row['type']][(string)$row['component_id']][(string)$row['value']] = $row['content'];
            }
        }

        return $ret;
    }

    public function savePreload(Vps_Component_Data $source, Vps_Component_Data $target)
    {
        if ($source->componentId == $target->componentId)
            throw new Vps_Exception('Source and target component must be different, both have ' . $source->componentId);
        $data = array(
            'page_id' => $source->getPage()->componentId,
            'preload_id' => $target->componentId
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['preload']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    protected function _getModelname($row)
    {
        if ($row instanceof Vps_Model_Row_Abstract) {
            $model = $row->getModel();
            if (get_class($model) == 'Vps_Model_Db') $model = $model->getTable();
        } else if ($row instanceof Zend_Db_Table_Row_Abstract) {
            $model = $row->getTable();
        } else {
            throw new Vps_Exception('row must be instance of Vps_Model_Row_Abstract or Zend_Db_Table_Row_Abstract');
        }
        return get_class($model);
    }
/*
    protected function _getRowComponentIds($row, $callback = 0)
    {
        $model = $this->getModel('metaRow');

        $select = $model->select()
            ->whereEquals('model', $this->_getModelname($row))
            ->whereEquals('callback', $callback);

        $columns = array();
        foreach ($model->getRows($select) as $r) {
            $columns[] = $r->column;
        }
        $columns = array_unique($columns);

        $and = array();
        foreach ($columns as $column) {
            $and[] = new Vps_Model_Select_Expr_And(array(
                new Vps_Model_Select_Expr_Equal('column', $column),
                new Vps_Model_Select_Expr_Equal('value', $row->$column)
            ));
        }
        $select->where(new Vps_Model_Select_Expr_Or($and));
        $componentIds = array();
        foreach ($model->getRows($select) as $r) {
            $componentIds[] = $r->component_id;
        }

        return $componentIds;
    }

    protected function _getModelComponentIds($row, $callback = 0)
    {
        $model = $this->getModel('metaModel');

        $select = $model->select()
            ->whereEquals('model', $this->_getModelname($row))
            ->whereEquals('callback', $callback);

        $componentIds = array();
        foreach ($model->getRows($select) as $metaRow) {
            $componentId = $metaRow->pattern;
            if (!$componentId) continue;
            $matches = array();
            preg_match_all('/\{([a-z0-9_]+)\}/', $componentId, $matches);
            foreach ($matches[1] as $m) {
                $componentId = str_replace('{' . $m . '}', $row->$m, $componentId);
            }
            $componentIds[$metaRow->component_class] = $componentId;
        }
        return $componentIds;
    }

    protected function _addMetaComponentIds($componentIds, $expr = null)
    {
        $model = $this->getModel('metaComponent');
        do {
            $select = $model->select();
            if ($expr) {
                $select->where($expr);
                $expr = null;
            } else {
                $select->whereEquals('component_id', $componentIds);
            }
            $componentIds2 = array();
            foreach ($model->getRows($select) as $r) {
                $componentIds2[] = $r->target_component_id;
            }
            $componentIds2 = array_diff($componentIds2, $componentIds);
            $componentIds = array_unique(array_merge($componentIds, $componentIds2));
        } while ($componentIds2);
        return $componentIds;
    }

    public function cleanByRow(Vps_Model_Row_Abstract $row)
    {
        $select = $this->getModel('cache')->select();
        $this->_chained = $this->getModel('metaChained')->getRows();

        // Cache
        $componentIds = $this->_getRowComponentIds($row);
        p($componentIds);
        $componentIds = $this->_addMetaComponentIds($componentIds);

        $modelComponentIds = $this->_getModelComponentIds($row);
        $or = array();
        foreach ($modelComponentIds as $componentClass => $componentId) {
            $or[] = new Vps_Model_Select_Expr_And(array(
                new Vps_Model_Select_Expr_Like('component_id', $componentId),
                new Vps_Model_Select_Expr_Equal('component_class', $componentClass)
            ));
        }
        $expr = new Vps_Model_Select_Expr_Or($or);
        $modelComponentIds = $this->_addMetaComponentIds($modelComponentIds, $expr);
        $componentIds = array_unique(array_merge($componentIds, array_values($modelComponentIds)));
        $or[] = new Vps_Model_Select_Expr_Equal('component_id', $componentIds);

        $expr = new Vps_Model_Select_Expr_Or($or);
        //d($this->getModel('cache')->select()->where($expr)->getParts());
        $this->getModel('cache')->updateRows(
            array('deleted' => 1),
            $this->getModel('cache')->select()->where($expr)
        );

        // Callback
        $componentIds = $this->_getRowComponentIds($row, 1);
        $componentIds = array_merge($componentIds, array_values($this->_getModelComponentIds($row, 1)));
        foreach ($componentIds as $componentId) {
            $component = Vps_Component_Data_Root::getInstance()->getComponentById(
                $componentId, array('ignoreVisible' => true)
            );
            if ($component) $component->getComponent()->onCacheCallback($row);
        }
    }
*/
    protected function _addRowComponentIds($componentIds, $row, $callback = 0)
    {
        // Das suchen wir
        // $searchModel = model column value component_id callback
        // $searchModel->model = $row->model && $searchModel->value=$row->{$searchModel->column}
        $searchModel = $this->getModel('metaRow');

        $select = $searchModel->select()
            ->whereEquals('model', $this->_getModelname($row))
            ->whereEquals('callback', $callback);

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
        $select->where(new Vps_Model_Select_Expr_Or($or));
        foreach ($searchModel->getRows($select) as $r) {
            $componentIds[$r->component_class][$r->component_id] = true;
        }
        return $componentIds;
    }

    protected function _addModelComponentIds($componentIds, $row, $callback = 0)
    {
        $model = $this->getModel('metaModel');

        $select = $model->select()
            ->whereEquals('model', $this->_getModelname($row))
            ->whereEquals('callback', $callback);

        foreach ($model->getRows($select) as $metaRow) {
            $componentId = $metaRow->pattern;
            if (!$componentId) continue;
            $matches = array();
            preg_match_all('/\{([a-z0-9_]+)\}/', $componentId, $matches);
            foreach ($matches[1] as $m) {
                $componentId = str_replace('{' . $m . '}', $row->$m, $componentId);
            }
            $componentIds[$metaRow->component_class][$componentId] = true;
        }
        return $componentIds;
    }

    protected function _addMetaComponentIds($componentIds)
    {
        // $model: component_id component_class target_component_id target_component_class
        // So lange $model nach component_id durchsuchen und in ergebnis
        // $model->target_component_id reingeben bis nichts mehr neues kommt
        $ids = array();
        foreach ($componentIds as $k => $c) $ids = array_merge($ids, array_keys($c));
        $model = $this->getModel('metaComponent');
        do {
            $select = $model->select();
            $select->whereEquals('component_id', $ids);
            $ids2 = array();
            foreach ($model->getRows($select) as $r) {
                $ids2[] = $r->target_component_id;
                $componentIds[$r->component_class][$r->target_component_id] = true;
            }
            $ids2 = array_diff($ids2, $ids);
            $ids = array_unique(array_merge($ids, $ids2));
        } while ($ids2);
        return $componentIds;
    }

    public function cleanByRow(Vps_Model_Row_Abstract $row)
    {
        $ids = array();
        $ids = $this->_addRowComponentIds($ids, $row);
        $ids = $this->_addModelComponentIds($ids, $row);
        $ids = $this->_addMetaComponentIds($ids);
        $or = array();
        $componentIds = array();
        foreach ($ids as $cClass => $cIds) {
            foreach (array_keys($cIds) as $cId) {
                if (strpos($cId, '%') !== false) {
                    $or[] = new Vps_Model_Select_Expr_And(array(
                        new Vps_Model_Select_Expr_Like('component_id', $cId),
                        new Vps_Model_Select_Expr_Equal('component_class', $cClass)
                    ));
                } else {
                    $componentIds[] = $cId;
                }
            }
        }

        $select = $this->getModel('cache')->select();
        $or[] = new Vps_Model_Select_Expr_Equal('component_id', $componentIds);
        $select->where(new Vps_Model_Select_Expr_Or($or));
        $this->getModel('cache')->updateRows(array('deleted' => 1), $select);

        // Callback
        $ids = array();
        $ids = $this->_addRowComponentIds($ids, $row, 1);
        $ids = $this->_addModelComponentIds($ids, $row, 1);
        foreach ($ids as $componentIds) {
            foreach (array_unique($componentIds) as $componentId) {
                $component = Vps_Component_Data_Root::getInstance()->getComponentById(
                    $componentId, array('ignoreVisible' => true)
                );
                if ($component) $component->getComponent()->onCacheCallback($row);
            }
        }
    }

    public function cleanByModel(Vps_Model_Abstract $model)
    {
        $select = $this->getModel('metaModel')->select()
            ->whereEquals('model', get_class($model))
            ->whereNull('pattern')
            ->whereEquals('callback', 0);
        $componentClasses = array();
        foreach ($this->getModel('metaModel')->getRows($select) as $r) {
            $componentClasses[] = $r->component_class;
        }
        $componentSelect = $this->getModel('metaComponent')->select()
            ->whereEquals('component_class', $componentClasses);
        foreach ($this->getModel('metaComponent')->getRows($componentSelect) as $r) {
            $componentClasses[] = $r->target_component_class;
        }
        $componentClasses = array_unique($componentClasses);
        $this->getModel('cache')->updateRows(
            array('deleted' => 1),
            $this->getModel('cache')->select()->whereEquals('component_class', $componentClasses)
        );
    }

    protected function _saveMetaModel($componentClass, $modelName, $pattern, $isCallback)
    {
        $data = array(
            'model' => $modelName,
            'component_class' => $componentClass,
            'pattern' => $pattern,
            'callback' => $isCallback ? 1 : 0
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['metaModel']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    protected function _saveMetaRow(Vps_Component_Data $component, $modelName, $column, $value, $isCallback)
    {
        // TODO: checken, ob component->componentClass eh nicht schon in cache_component_meta_model mit gleichem Model steht
        $data = array(
            'model' => $modelName,
            'column' => $column,
            'value' => $value,
            'component_id' => $component->componentId,
            'component_class' => $component->componentClass,
            'callback' => $isCallback ? 1 : 0
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models[$type]->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
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
}
