<?php
abstract class Vps_Filter_Row_Abstract implements Zend_Filter_Interface
{
    protected $_field = null;

    //bekommt eine row übergeben
    public function filter($row)
    {
    }

    public function setField($field)
    {
        $this->_field = $field;
    }

    public function onDeleteRow($row)
    {
    }
    
    public function filterAfterSave()
    {
        return false;
    }

    public function skipFilter($row, $column)
    {
        return false;
    }
}
