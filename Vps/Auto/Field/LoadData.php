<?php
class Vps_Auto_Field_LoadData extends Vps_Auto_Field_SimpleAbstract
{

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        unset($ret['type']);
        return $ret;
    }

    public function save(Zend_Db_Table_Row_Abstract $row, $postData)
    {
    }
}
