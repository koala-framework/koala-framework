<?php
/**
 * Text field where only a date input is valid. Datepicker is shown in ExtJS and Frontend
 * If you want to use this class please add
 *    Admin.dep[] = ExtFormDateField
 * to your "dependencies.ini
 *
 * @package Form
 */
class Kwf_Form_Field_DateField extends Kwf_Form_Field_SimpleAbstract
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setPlaceholderText(trlKwfStatic('yyyy-mm-dd'));
        $this->setXtype('datefield');
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'placeholderText';
        return $ret;
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
        $this->addValidator(new Kwf_Validate_Date(array('outputFormat' => trlKwf('yyyy-mm-dd'))));
    }

    protected function _getValueFromPostData($postData)
    {
        $ret = parent::_getValueFromPostData($postData);
        if ($ret == $this->getPlaceholderText()) return null;
        if (!$ret) return null;

        //format das von ext kommt
        if (preg_match('#"(\d{4}-\d{2}-\d{2})T\d{2}:\d{2}:\d{2}"#', $ret, $m)) {
            return $m[1];
        }

        //format das vom frontend kommt
        if (!strtotime($ret)) return $ret;
        return date('Y-m-d', strtotime($ret));
    }

    protected function _getOutputValueFromValues($values)
    {
        $name = $this->getFieldName();
        $ret = isset($values[$name]) ? $values[$name] : $this->getDefaultValue();
        return (string)$ret;
    }

    public function getFrontendMetaData()
    {
        $ret = parent::getFrontendMetaData();
        $ret['hideTrigger'] = $this->getHideTrigger();
        return $ret;
    }

    /**
     * @deprecated
     */
    public function getHideDatePicker()
    {
        return $this->getHideTrigger();
    }

    /**
     * @deprecated
     */
    public function setHideDatePicker($enable)
    {
        return $this->setHideTrigger($enable);
    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $name = $this->getFieldName();
        $value = $this->_getOutputValueFromValues($values);
        if (!$value) $value = $this->getPlaceholderText();
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);

        $class = '';
        if ($value != $this->getPlaceholderText()) {
            $v = strtotime($value);
            if ($v) $value = date(trlKwf('Y-m-d'), $v);
        } else {
            $class = 'kwfClearOnFocus';
        }

        $value = htmlspecialchars($value);
        $name = htmlspecialchars($name);
        $ret['id'] = $idPrefix.str_replace(array('[', ']'), array('_', '_'), $name.$fieldNamePostfix);
        $ret['html'] = "<input class=\"$class\" type=\"text\" id=\"$ret[id]\" name=\"$name$fieldNamePostfix\" value=\"$value\" style=\"width: {$this->getWidth()}px\" maxlength=\"{$this->getMaxLength()}\"/>";
        return $ret;
    }

    /**
     * Hides the date-picker icon next to textarea if set to false
     * @param boolean
     * @return $this
     */
    public function setHideTrigger($value)
    {
        return $this->setProperty('hideTrigger', $value);
    }
}
