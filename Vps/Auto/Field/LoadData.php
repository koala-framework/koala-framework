<?php
class Vps_Auto_Field_LoadData extends Vps_Auto_Field_SimpleAbstract
{
    public function getMetaData()
    {
        return null;
    }

    public function prepareSave(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        Vps_Auto_Field_Abstract::prepareSave($row, $postData);
    }
}
