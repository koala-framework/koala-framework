<?php
class Kwc_Form_Container_FieldSet_Component extends Kwc_Form_Container_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Form.Fieldset');
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Kwf_Form_Container_FieldSet();
        $ret->setTitle($this->getRow()->title);
        return $ret;
    }
}