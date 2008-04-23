<?php
class Vps_Form_Field_DateField extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('datefield');
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Zend_Validate_Date());
    }

    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if ($ret == '') $ret = null;
        if ($ret) { 
            $ret = substr(str_replace('"', '', $ret), 0, 10); 
        }
        return $ret;
    }
}
