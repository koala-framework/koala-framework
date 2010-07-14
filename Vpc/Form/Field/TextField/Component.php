<?php
class Vpc_Form_Field_TextField_Component extends Vpc_Form_Field_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Form.Textfield');
        $ret['componentIcon'] = new Vps_Asset('textfield');
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Vps_Form_Field_TextField($this->getData()->componentId);
        $ret->setFieldLabel($this->getRow()->field_label);
        $ret->setWidth($this->getRow()->width);
        $ret->setDefaultValue($this->getRow()->default_value);
        $ret->setAllowBlank(!$this->getRow()->required);
        $ret->setVtype($this->getRow()->vtype);
        return $ret;
    }
}
