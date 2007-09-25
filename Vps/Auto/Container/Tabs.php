<?php
class Vps_Auto_Container_Tabs extends Vps_Auto_Container_Abstract
{
    protected $_xtype = 'tabpanel';
    public $tabs;

    public function __construct($name = null)
    {
        $this->tabs = new Vps_Collection('Vps_Auto_Container_Tab');
        parent::__construct($name);
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        $ret['items'] = array();
        foreach ($this->tabs as $field) {
            $ret['items'][] = $field->getMetaData();
        }
        if (!isset($ret['defaults']['autoHeight'])) $ret['defaults']['autoHeight'] = true;
        if (!isset($ret['defaults']['bodyStyle'])) $ret['defaults']['bodyStyle'] = 'padding:10px';
        return $ret;
    }

    public function getByName($name)
    {
        $ret = parent::getByName($name);
        if($ret) return $ret;
        return $this->tabs->getByName($name);
    }
    public function hasChildren()
    {
        return sizeof($this->tabs) > 0;
    }
    public function getChildren()
    {
        return $this->tabs;
    }

    public function add($v = null)
    {
        return $this->tabs->add($v);
    }
}
