<?php
class Vps_Auto_Data_Table extends Vps_Auto_Data_Abstract
{
    public function load($row)
    {
        $name = $this->getFieldname();
        if (!isset($row->$name) && !is_null($row->$name)) { //scheiß php
            throw new Vps_Exception("Index '$name' doesn't exist in row.");
        }
        return $row->$name;
    }

    public function save(Zend_Db_Table_Row_Abstract $row, $data)
    {
        $name = $this->getFieldname();
        $row->$name = $data;
    }
}
