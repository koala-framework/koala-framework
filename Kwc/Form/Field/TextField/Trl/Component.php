<?php
class Kwc_Form_Field_TextField_Trl_Component extends Kwc_Form_Field_Abstract_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
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