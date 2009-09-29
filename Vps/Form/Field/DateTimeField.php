<?php
class Vps_Form_Field_DateTimeField extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('vps.datetime');
    }

    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if (preg_match('#^"([0-9]{4}-[0-9]{2}-[0-9]{2})T([0-9]{2}:[0-9]{2}:[0-9]{2})"$#', $ret, $m)) {
            $ret = "$m[1] $m[2]";
	} else {
	    $ret = null;
	}
        return $ret;
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        //TODO: $this->addValidator(new Zend_Validate_Date());
    }
}
