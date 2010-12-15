<?php
class Vpc_Form_Field_Select_Component extends Vpc_Form_Field_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Form.Select');
        $ret['ownModel'] = 'Vpc_Form_Field_Select_Model';
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Vps_Form_Field_Select($this->getData()->componentId);
        $ret->setFieldLabel($this->getRow()->field_label);
        $ret->setWidth($this->getRow()->width);
        $ret->setAllowBlank(!$this->getRow()->required);
        $values = array();
        foreach ($this->getRow()->getChildRows('Values') as $i) {
            $values[$i->value] = $i->value;
        }
        $ret->setValues($values);
        return $ret;
    }
}