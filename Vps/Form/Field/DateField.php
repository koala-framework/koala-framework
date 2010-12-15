<?php
class Vps_Form_Field_DateField extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('datefield');
    }

    protected function _processLoaded($v)
    {
        if (strlen($v) > 10) {
            $v = substr($v, 0, 10);
        }
        return $v;
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Zend_Validate_Date());
    }

    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if ($ret == trlVps('yyyy-mm-dd')) return null;
        if (!$ret) return null;

        //format das von ext kommt
        if (preg_match('#"(\d{4}-\d{2}-\d{2})T\d{2}:\d{2}:\d{2}"#', $ret, $m)) {
            return $m[1];
        }

        //format das vom frontend kommt
        if (!strtotime($ret)) return null;
        return date('Y-m-d', strtotime($ret));
    }

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $name = $this->getFieldName();
        $value = $values[$name];
        if (!$value) $value = trlVps('yyyy-mm-dd');
        $ret = parent::getTemplateVars($values, $fieldNamePostfix);

        $value = htmlspecialchars($value);
        $name = htmlspecialchars($name);
        $ret['id'] = str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $ret['html'] = "<input type=\"text\" id=\"$ret[id]\" name=\"$name$fieldNamePostfix\" value=\"$value\" style=\"width: {$this->getWidth()}px\" maxlength=\"{$this->getMaxLength()}\"/>";
        return $ret;
    }
}
