<?php
class Kwc_Trl_Columns_Columns_Column_Form extends Kwc_Abstract_Form
{
    protected function _initFields()
    {
        $ret = parent::_initFields();
        $this->add(new Kwf_Form_Field_TextField('test', 'Test'));
        return $ret;
    }
}
