<?php
/**
 * @package Form
 */
class Kwf_Form_Field_TimeField extends Kwf_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('timefield');
        $this->setFormat('H:i');
        $this->setSaveSeconds(true);
    }

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        if (!isset($ret['width'])) $ret['width'] = 70;
        return $ret;
    }


    protected function _addValidators()
    {
        parent::_addValidators();
        $this->addValidator(new Kwf_Validate_Time());
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
        if ($ret == trlKwf('hh:mm')) $ret = null;
        if ($ret == '') $ret = null;
        if ($ret) $ret = str_replace('"', '', $ret);
        return $ret;
    }

    protected function _getValueToSaveFromPostData($postData)
    {
        $ret = parent::_getValueToSaveFromPostData($postData);
        if ($ret && $this->getSaveSeconds()) {
            $ret .= ':00';
        }
        return $ret;
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $name = $this->getFieldName();
        $value = $values[$name];
        if (!$value) $value = trlKwf('hh:mm');
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);

        $value = Kwf_Util_HtmlSpecialChars::filter($value);
        $name = Kwf_Util_HtmlSpecialChars::filter($name);
        $ret['id'] = $idPrefix.str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $ret['html'] = "<input type=\"text\" id=\"$ret[id]\" name=\"$name$fieldNamePostfix\" value=\"$value\" style=\"width: {$this->getWidth()}px\" maxlength=\"{$this->getMaxLength()}\"/>";
        return $ret;
    }
}
