<?php
/**
 * Basisklasse für Fields die andere Fields beinhalten
 *
 * zB FieldSet
 **/
abstract class Vps_Auto_Container_Abstract extends Vps_Auto_Field_Abstract
{
    public $fields;

    public function __construct($name = null)
    {
        parent::__construct($name);
        $this->fields = new Vps_Collection();
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        $ret['items'] = $this->fields->getMetaData();
        return $ret;
    }

    public function getByName($name)
    {
        $ret = parent::getByName($name);
        if($ret) return $ret;
        return $this->fields->getByName($name);
    }

    public function hasChildren()
    {
        return sizeof($this->fields) > 0;
    }
    public function getChildren()
    {
        return $this->fields;
    }

    public function add($v = null)
    {
        return $this->fields->add($v);
    }
}
