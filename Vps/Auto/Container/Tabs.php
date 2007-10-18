<?php
class Vps_Auto_Container_Tabs extends Vps_Auto_Container_Abstract
{
    public $tabs;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->tabs = new Vps_Collection('Vps_Auto_Container_Tab');
        //$this->setDeferredRender(false); auskommentiert wegen combobox-view-breite-bug
        $this->setBaseCls('x-plain');
        $this->setXtype('tabpanel');
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        $ret['items'] = $this->tabs->getMetaData();
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
        $return = $this->tabs->add($v);
        $return->setTitle($v);
        return $return;
    }
}
