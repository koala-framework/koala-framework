<?php
class Vps_Auto_Field_Fieldset extends Vps_Auto_Field_Abstract
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
        $ret['fields'] = array();
        foreach ($this->fields as $field) {
            $ret['fields'][] = $field->getMetaData();
        }
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
}
