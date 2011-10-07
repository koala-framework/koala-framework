<?php
class Vpc_Form_Field_Checkbox_Trl_Component extends Vpc_Form_Field_Abstract_Trl_Component
{
    protected function _getFormField()
    {
        $ret = parent::_getFormField();
        if ($this->getRow()->box_label) $ret->setBoxLabel($this->getRow()->box_label);
        return $ret;
    }
}