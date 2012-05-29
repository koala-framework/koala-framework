<?php
class Kwc_Form_Field_Radio_Component extends Kwc_Form_Field_Select_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlKwfStatic('Form.Radio');
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Kwf_Form_Field_Radio($this->getData()->componentId);
        $ret->setFieldLabel($this->getRow()->field_label);
        $ret->setOutputType($this->getRow()->output_type);
        $ret->setAllowBlank(!$this->getRow()->required);
        $ret->setHideLabel($this->getRow()->hide_label);
        $values = array();
        foreach ($this->getRow()->getChildRows('Values') as $i) {
            $values[$i->value] = $i->value;
        }
        $ret->setValues($values);
        return $ret;
    }
}
