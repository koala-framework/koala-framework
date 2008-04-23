<?php
class Vps_Form_Field_ShowField extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('showfield');
    }
    public function prepareSave(Vps_Model_Row_Interface $row, $postData)
    {
    }
}
