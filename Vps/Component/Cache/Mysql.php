<?php
class Vps_Component_Cache_Mysql
{
    protected $_models;

    public function __construct()
    {
        $this->_models = array (
            'cache' => new Vps_Component_Cache_Mysql_Model(),
            'preload' => new Vps_Component_Cache_Mysql_PreloadModel(),
            'metaModel' => new Vps_Component_Cache_Mysql_MetaModelModel(),
            'metaRow' => new Vps_Component_Cache_Mysql_MetaRowModel(),
            'metaCallback' => new Vps_Component_Cache_Mysql_MetaCallbackModel(),
            'metaComponent' => new Vps_Component_Cache_Mysql_MetaComponentModel()
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
            ->whereEquals('value', $value);
        $row = $this->_models['cache']->export(Vps_Model_Db::FORMAT_ARRAY, $select);
        if ($row) return $row[0]['content'];
        return null;
    }

    public function preload(Vps_Component_Data $component)
    {
        $ret = array();

        $select = $this->_models['cache']->select();
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

    public function saveMetaModel(Vps_Component_Data $component, $model)
    {
        if (is_object($model) && get_class($model) == 'Vps_Model_Db') $model = $model->getTable();
        if (!is_string($model)) $model = get_class($model);
        $data = array(
            'model' => $model,
            'component_class' => $component->componentClass
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['metaModel']()->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    public function saveMetaRow(Vps_Component_Data $component, $row, $field = null, $type = 'metaRow')
    {
        if (!$row instanceof Vps_Model_Row_Abstract &&
            !$row instanceof Zend_Db_Table_Row_Abstract
        ) throw new Vps_Exception('Row must be instance of Vps_Model_Row_Abstract or Zend_Db_Table_Row_Abstract');

        if (get_class($row) == 'Vps_Model_Db_Row' || $row instanceof Zend_Db_Table_Row) {
            $row = $row->getRow();
            $modelName = get_class($row->getTable());
            if (!$field) $field = current($row->getTable()->info('primary'));
        } else {
            $modelName = get_class($row->getModel());
            if (!$field) $field = $row->getModel()->getPrimaryKey();
        }
        $id = $row->$field;

        $data = array(
            'model' => $modelName,
            'field' => $field,
            'value' => $id,
            'component_id' => $component->componentId
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models[$type]->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
    }

    public function saveMetaCallback(Vps_Component_Data $component, $row, $field = null)
    {
        $this->saveMetaRow($component, $row, $field, 'metaCallback');
    }

    public function saveMetaComponent(Vps_Component_Data $source, Vps_Component_Data $target)
    {
        if ($source->componentId == $target->componentId)
            throw new Vps_Exception('Source and target component must be different, both have ' . $source->componentId);
        $data = array(
            'component_id' => $target->componentId,
            'component_class' => $target->componentClass,
            'source_component_id' => $source->componentId,
            'source_component_class' => $source->componentClass
        );
        $options = array(
            'buffer' => true,
            'replace' => true
        );
        $this->_models['metaComponent']->import(Vps_Model_Abstract::FORMAT_ARRAY, array($data), $options);
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

    protected function _getComponentIds($row, $metaModel)
    {
        $select = $metaModel->select()
            ->whereEquals('model', get_class($row->getModel()));
        $fields = array();
        foreach ($metaModel->getRows($select) as $r) {
            $fields[] = $r->field;
        }
        $fields = array_unique($fields);

        $and = array();
        foreach ($fields as $field) {
            if ($field == '') {
                $primaryKey = $row->getModel()->getPrimaryKey();
                $value = $row->$primaryKey;
            } else if (!$row->hasColumn($field)) {
                continue;
            } else {
                $value = $row->$field;
            }
            $and[] = new Vps_Model_Select_Expr_And(array(
                new Vps_Model_Select_Expr_Equals('field', $field),
                new Vps_Model_Select_Expr_Equals('value', $value)
            ));
        }
        $select->where(new Vps_Model_Select_Expr_Or($and));
        $componentIds = array();
        foreach ($metaModel->getRows($select) as $r) {
            $componentIds[] = $r->component_id;
        }

        do {
            $componentSelect = $this->getModel('metaComponent')->select();
            $componentSelect->whereEquals('source_component_id', $componentIds);
            $componentIds2 = array();
            foreach ($this->getModel('metaComponent')->getRows($componentSelect) as $r) {
                $componentIds2[] = $r->component_id;
            }
            $componentIds2 = array_diff($componentIds2, $componentIds);
            $componentIds = array_unique(array_merge($componentIds, $componentIds2));
        } while ($componentIds2);

        return $componentIds;
    }

    public function cleanByRow(Vps_Model_Row_Abstract $row)
    {
        $componentIds = $this->_getComponentIds($row, $this->getModel('metaRow'));
        $this->getModel('cache')->deleteRows(
            $this->getModel('cache')->select()->whereEquals('component_id', $componentIds)
        );
        foreach ($componentIds as $componentId) {
            $component = Vps_Component_Data_Root::getInstance()
                ->getComponentById($componentId, array('ignoreVisible' => true));
            if ($component) $component->getComponent()->onCacheCallback($row);
        }
    }

    public function cleanByModel(Vps_Model_Abstract $model)
    {
        $select = $this->getModel('metaModel')->select()
            ->whereEquals('model', get_class($model));
        $componentClasses = array();
        $componentSelect = $this->getModel('metaComponent')->select();
        foreach ($this->getModel('metaModel')->getRows($select) as $r) {
            $componentClasses[] = $r->component_class;
            $componentSelect->whereEquals('source_component_class', $r->component_class);
        }
        foreach ($this->getModel('metaComponent')->getRows($componentSelect) as $r) {
            $componentClasses[] = $r->component_class;
        }
        $componentClasses = array_unique($componentClasses);
        $this->getModel('cache')->deleteRows(
            $this->getModel('cache')->select()->whereEquals('component_class', $componentClasses)
        );
    }
}
