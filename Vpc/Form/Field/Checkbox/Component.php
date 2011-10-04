<?php
class Vpc_Form_Field_Checkbox_Component extends Vpc_Form_Field_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Form.Checkbox');
        $ret['componentIcon'] = new Vps_Asset('textfield'); //TODO besseres icon
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Vps_Form_Field_Checkbox($this->getData()->componentId);
        $ret->setFieldLabel($this->getRow()->field_label);
        $ret->setBoxLabel($this->getRow()->box_label);
        $ret->setDefaultValue($this->getRow()->default_value);
        $ret->setAllowBlank(!$this->getRow()->required);
        $ret->setHideLabel($this->getRow()->hide_label);
        return $ret;
    }
}
