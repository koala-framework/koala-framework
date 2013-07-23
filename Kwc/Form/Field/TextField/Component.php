<?php
class Kwc_Form_Field_TextField_Component extends Kwc_Form_Field_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Form.Textfield');
        $ret['componentIcon'] = new Kwf_Asset('textfield');
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Kwf_Form_Field_TextField($this->getData()->componentId);
        $ret->setFieldLabel($this->getRow()->field_label);
        if ($this->getRow()->label_width) $ret->setLabelWidth($this->getRow()->label_width);
        $ret->setWidth($this->getRow()->width);
        $ret->setDefaultValue($this->getRow()->default_value);
        $ret->setAllowBlank(!$this->getRow()->required);
        $ret->setHideLabel($this->getRow()->hide_label);
        $ret->setVtype($this->getRow()->vtype);
        if ($this->getRow()->label_position_above) {
            $ret->setLabelPosition('above');
        }
        return $ret;
    }
}
