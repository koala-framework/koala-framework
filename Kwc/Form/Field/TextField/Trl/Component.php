<?php
class Vpc_Form_Field_TextField_Trl_Component extends Vpc_Form_Field_Abstract_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = parent::_getFormField();
        $ret->setDefaultValue($this->getRow()->default_value);
        return $ret;
    }
}