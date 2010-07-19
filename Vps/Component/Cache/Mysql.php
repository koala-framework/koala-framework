<?php
class Vps_Component_Cache_Mysql
{
    protected $_models;

    public function __construct()
    {
        $this->_models = array (
            'cache' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_Model'),
            'preload' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_PreloadModel'),
            'metaModel' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_MetaModelModel'),
            'metaRow' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_MetaRowModel'),
            'metaCallback' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_MetaCallbackModel'),
            'metaComponent' => Vps_Model_Abstract::getInstance('Vps_Component_Cache_Mysql_MetaComponentModel')
        );
    }

    public function save(Vps_Component_Data $component, $content, $type = 'component', $value = null)
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

    public function load(Vps_Component_Data $component)
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
            'id' => $id,
            'component_class' => $component->componentClass
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

    public function clean() {}

}
