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
        if ($ret == trlVps('yyyy-mm-dd')) $ret = null;
        if ($ret == '') $ret = null;
        if ($ret) { 
            $ret = substr(str_replace('"', '', $ret), 0, 10); 
        }
        if ($ret) {
            $ret = date('Y-m-d', strtotime($ret));
        }
        return $ret;
    }

    public function getTemplateVars($values)
    {
        $name = $this->getFieldName();
        if (isset($values[$name])) {
            $value = $values[$name];
        } else {
            $value = $this->getDefaultValue();
        }
        if (!$value) $value = trlVps('yyyy-mm-dd');
        $ret = parent::getTemplateVars($values);

        $value = htmlspecialchars($value);
        $name = htmlspecialchars($name);
        $ret['html'] = "<input type=\"text\" id=\"$name\" name=\"$name\" value=\"$value\" style=\"width: {$this->getWidth()}px\" maxlength=\"{$this->getMaxLength()}\"/>";
        return $ret;
    }
}
