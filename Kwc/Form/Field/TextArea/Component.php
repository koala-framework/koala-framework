<?php
class Kwc_Form_Field_TextArea_Component extends Kwc_Form_Field_TextField_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Form.Textarea');
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Kwf_Form_Field_TextArea($this->getData()->componentId);
        $ret->setFieldLabel($this->getRow()->field_label);
        if ($this->getRow()->label_width) $ret->setLabelWidth($this->getRow()->label_width);
        $ret->setWidth($this->getRow()->width);
        $ret->setHeight($this->getRow()->height);
        $ret->setDefaultValue($this->getRow()->default_value);
        $ret->setHideLabel($this->getRow()->hide_label);
        $ret->setAllowBlank(!$this->getRow()->required);
        if ($this->getRow()->label_position_above) {
            $ret->setLabelPosition('above');
        }
        return $ret;
    }
}