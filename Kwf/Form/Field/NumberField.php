<?php
/**
 * Numeric text field
 *
 * ExtJS provides automatic keystroke filtering and numeric validation.
 *
 * @package Form
 */
class Kwf_Form_Field_NumberField extends Kwf_Form_Field_TextField
{
    private $_floatValidator;

    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('numberfield');
        $this->setDecimalSeparator(trlcKwf('decimal separator', '.'));
        $this->setDecimalPrecision(2);
        $this->setInputType('number');

        $this->_floatValidator = new Zend_Validate_Float();
    }

    public function trlStaticExecute($language = null)
    {
        parent::trlStaticExecute($language);
        $locale = Kwf_Trl::getInstance()->trlc('locale', 'C', Kwf_Trl::SOURCE_KWF, $language);
        if ($locale != 'C') {
            $l = Zend_Locale::findLocale($locale);
            $this->_floatValidator->setLocale($l);
        }
    }

    protected function _addValidators()
    {
        parent::_addValidators();

        if ($this->getMaxValue()) {
            $this->addValidator(new Kwf_Validate_MaxValue($this->getMaxValue()));
        }
        if ($this->getMinValue()) {
            $this->addValidator(new Kwf_Validate_MinValue($this->getMinValue()));
        }
        if ($this->getAllowNegative() === false) {
            $this->addValidator(new Kwf_Validate_NotNegative());
        }
        if ($this->getAllowDecimals() === false) {
            $this->addValidator(new Kwf_Validate_Digits());
        } else {
            $this->addValidator($this->_floatValidator);
        }
    }

    protected function _getValueToSaveFromPostData($postData)
    {
        $ret = parent::_getValueToSaveFromPostData($postData);
        $l = $this->_floatValidator->getLocale();
        $ret = Zend_Locale_Format::getNumber($ret, array('locale' => $l));
        return $ret;
    }

    protected function _getValueFromPostData($postData)
    {
        $fieldName = $this->getFieldName();
        if (!isset($postData[$fieldName])) $postData[$fieldName] = null;
        if ($postData[$fieldName] === '') {
            $postData[$fieldName] = null;
        }
        return $postData[$fieldName];
    }

    protected function _getOutputValueFromValues($values)
    {
        $ret = parent::_getOutputValueFromValues($values);
        if (!$ret) return '';
        $ret = number_format((float)$ret, $this->getDecimalPrecision(), $this->getDecimalSeparator(), '');
        return $ret;
    }

    protected function _getInputProperties($values, $fieldNamePostfix, $idPrefix)
    {
        $ret = parent::_getInputProperties($values, $fieldNamePostfix, $idPrefix);
        if ($this->getMaxValue()) {
            $ret['max'] = $this->getMaxValue();
        }
        if ($this->getMinValue()) {
            $ret['min'] = $this->getMaxValue();
        }
        if ($this->getAllowNegative() === false) {
            if (!isset($ret['min']) || $ret['min'] > 0) {
                $ret['min'] = 0;
            }
        }
        if ($this->getAllowDecimals() === false) {
            $ret['step'] = '1';
        }
        return $ret;

    }

    /**
     * The maximum allowed value
     *
     * @param float
     * @return $this
     */
    public function setMaxValue($value)
    {
        return $this->setProperty('maxValue', $value);
    }

    /**
     * The minimum allowed value
     *
     * @param float
     * @return $this
     */
    public function setMinValue($value)
    {
        return $this->setProperty('minValue', $value);
    }

    /**
     * False to prevent entering a negative sign (defaults to true)
     *
     * @param bool
     * @return $this
     */
    public function setAllowNegative($value)
    {
        return $this->setProperty('allowNegative', $value);
    }

    /**
     * False to disallow decimal values (defaults to true)
     *
     * @param bool
     * @return $this
     */
    public function setAllowDecimals($value)
    {
        return $this->setProperty('allowDecimals', $value);
    }

    /**
     * Character(s) to allow as the decimal separator (default depends on current language)
     *
     * @param string
     * @return $this
     */
    public function setDecimalSeparator($value)
    {
        return $this->setProperty('decimalSeparator', $value);
    }

    /**
     * The maximum precision to display after the decimal separator (defaults to 2)
     *
     * @param int
     * @return $this
     */
    public function setDecimalPrecision($value)
    {
        return $this->setProperty('decimalPrecision', $value);
    }
}
