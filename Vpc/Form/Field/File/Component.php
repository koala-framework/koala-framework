<?php
class Vpc_Form_Field_File_Component extends Vpc_Form_Field_Abstract_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Form.File Upload');
        $ret['componentIcon'] = new Vps_Asset('textfield');
        return $ret;
    }

    protected function _getFormField()
    {
        $ret = new Vps_Form_Field_File($this->getData()->componentId);
        $ret->setFieldLabel($this->getRow()->field_label);
        $ret->setAllowBlank(!$this->getRow()->required);
        return $ret;
    }
}