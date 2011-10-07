<?php
class Vpc_Trl_Columns_Columns_Column_Trl_Form extends Vpc_Abstract_Form
{
    protected function _initFields()
    {
        $ret = parent::_initFields();
        $this->add(new Vps_Form_Field_TextField('test', 'Test Trl'));
        return $ret;
    }
}
