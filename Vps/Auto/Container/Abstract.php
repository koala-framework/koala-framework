<?php
/**
 * Basisklasse für Fields die andere Fields beinhalten
 *
 * zB FieldSet
 **/
abstract class Vps_Auto_Container_Abstract extends Vps_Auto_Field_Abstract implements IteratorAggregate
{
    public $fields;

    public function __construct($name = null)
    {
        parent::__construct($name);
        if (!isset($this->fields)) {
            $this->fields = new Vps_Collection();
        }
    }

    public function getMetaData()
    {
        $iterator = new RecursiveIteratorIterator(new Vps_Collection_Iterator_Recursive($this->fields));
        foreach ($iterator as $field) {
            if ($field instanceof Vps_Auto_Field_File) {
                $this->setFileUpload(true);
            }
            if ($field instanceof Vps_Auto_Field_File
                || $field instanceof Vps_Auto_Field_HtmlEditor) {
                $this->setLoadAfterSave(true);
            }
        }
        $ret = parent::getMetaData();
        $ret['name'] = $this->getName();
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

    //IteratorAggregate
    public function getIterator()
    {
        return $this->fields->getIterator();
    }
}
