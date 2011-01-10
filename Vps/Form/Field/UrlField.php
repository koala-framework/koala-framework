<?php
class Vps_Form_Field_UrlField extends Vps_Form_Field_TextField
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setVtype('url');
    }

    protected function _processLoaded($value)
    {
        $value = parent::_processLoaded($value);
        $punycode = new Vps_Util_Punycode();
        $value = $punycode->decode($value);
        return $value;
    }

    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        $punycode = new Vps_Util_Punycode();
        $ret = $punycode->encode($ret);
        return $ret;
    }
}
