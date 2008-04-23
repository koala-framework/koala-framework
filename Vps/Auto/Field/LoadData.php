<?php
class Vps_Auto_Field_LoadData extends Vps_Auto_Field_SimpleAbstract
{
    public function getMetaData()
    {
        return null;
    }

    public function prepareSave(Vps_Model_Row_Interface $row, $postData)
    {
        Vps_Auto_Field_Abstract::prepareSave($row, $postData);
    }
}
