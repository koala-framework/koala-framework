<?php
class Vps_Form_Field_TimeField extends Vps_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('timefield');
        $this->setFormat('H:i');
        $this->setWidth(70);
    }

    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Vps_Validate_Time());
    }

    protected function _processLoaded($v)
    {
        if (strlen($v) > 5) {
            $v = substr($v, 0, 5);
        }
        return $v;
    }

    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if ($ret == trlVps('hh:mm')) $ret = null;
        if ($ret == '') $ret = null;
        if ($ret) $ret = str_replace('"', '', $ret);
        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '')
    {
        $name = $this->getFieldName();
        $value = $values[$name];
        if (!$value) $value = trlVps('hh:mm');
        $ret = parent::getTemplateVars($values, $fieldNamePostfix);

        $value = htmlspecialchars($value);
        $name = htmlspecialchars($name);
        $ret['id'] = str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $ret['html'] = "<input type=\"text\" id=\"$ret[id]\" name=\"$name$fieldNamePostfix\" value=\"$value\" style=\"width: {$this->getWidth()}px\" maxlength=\"{$this->getMaxLength()}\"/>";
        return $ret;
    }
}
