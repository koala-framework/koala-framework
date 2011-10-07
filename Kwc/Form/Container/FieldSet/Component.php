<?php
class Vpc_Form_Container_FieldSet_Component extends Vpc_Form_Container_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Form.Fieldset');
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Vps_Form_Container_FieldSet();
        $ret->setTitle($this->getRow()->title);
        return $ret;
    }
}