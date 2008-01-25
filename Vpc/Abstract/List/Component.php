<?php
abstract class Vpc_Abstract_List_Component extends Vpc_Abstract
{
    public $children = array();

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'componentName' => 'List',
            'tablename'     => 'Vpc_Abstract_List_Model',
            'childComponentClasses' => array(
                'child'         => 'Vpc_Empty'
            )
        ));
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Abstract/List/Panel.js';
        return $ret;
    }

    protected function _init()
    {
        $class = $this->_getClassFromSetting('child', 'Vpc_Abstract');
        $where = array(
            'page_id = ?' => $this->getDbId(),
            'component_key = ?' => $this->getComponentKey(),
            'component_class = ?' => $class
        );
        if (!$this->showInvisible()) {
            $where['visible = ?'] = 1;
        }
        foreach ($this->getTable()->fetchAll($where) as $row) {
            $this->children[$row->id] = $this->createComponent($class, $row->id);
        }
    }

    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        $return['children'] = array();
        foreach ($this->children as $c) {
            $return['children'][] = $c->getTemplateVars();
        }
        return $return;
    }

    public function getChildComponents()
    {
        return $this->children;
    }

}
