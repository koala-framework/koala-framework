<?php
class Vps_Auto_Field_ShowField extends Vps_Auto_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('showfield');
    }
    public function prepareSave(Zend_Db_Table_Row_Abstract $row, $postData)
    {
    }
}
