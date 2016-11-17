<?php
class Kwc_Form_Field_Recaptcha_Component extends Kwc_Form_Field_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = trlKwfStatic('Form.Recaptcha');
        $ret['componentIcon'] = 'lock_go';
        $ret['componentPriority'] = -1;
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Kwf_Form_Field_Recaptcha($this->getData()->componentId);
        $ret->setFieldLabel($this->getRow()->field_label);
        return $ret;
    }
}
