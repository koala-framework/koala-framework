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

    public function onDeleteRow(Vps_Db_Table_Row_Abstract $row)
    {
    }
}
