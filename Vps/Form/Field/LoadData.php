<?php
class Vps_Form_Field_LoadData extends Vps_Form_Field_SimpleAbstract
{
    public function getMetaData($model)
    {
        return null;
    }

    public function prepareSave(Vps_Model_Row_Interface $row, $postData)
    {
        Vps_Form_Field_Abstract::prepareSave($row, $postData);
    }
}
