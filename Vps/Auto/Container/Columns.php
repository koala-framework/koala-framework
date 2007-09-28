<?php
class Vps_Auto_Container_Columns extends Vps_Auto_Container_Abstract
{
    public $columns;

    public function __construct($name = null)
    {
        $this->columns = new Vps_Collection('Vps_Auto_Container_Column');
        parent::__construct($name);
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        $ret['items'] = array();
        foreach ($this->columns as $field) {
            $ret['items'][] = $field->getMetaData();
        }
        if (!isset($ret['layout'])) $ret['layout'] = 'column';
        if (!isset($ret['border'])) $ret['border'] = false;
        if (!isset($ret['baseCls'])) $ret['baseCls'] = 'x-plain';
        return $ret;
    }

    public function getByName($name)
    {
        $ret = parent::getByName($name);
        if($ret) return $ret;
        return $this->columns->getByName($name);
    }
    public function hasChildren()
    {
        return sizeof($this->columns) > 0;
    }
    public function getChildren()
    {
        return $this->columns;
    }

    public function add($v = null)
    {
        return $this->columns->add($v);
    }
}
