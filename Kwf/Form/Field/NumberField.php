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
        $this->setDecimalSeparator(trlcKwfStatic('decimal separator', '.'));
        $this->setAllowDecimals(true);
        $this->setDecimalPrecision(2);
       $this->setInputType('number');

        $this->_floatValidator = new Kwf_Validate_Float();
    }

    public function getMetaData($model)
    {
        $ret = parent::getMetaData($model);
        $ret['inputType'] = 'text'; //don't use type=number in ExtJs forms as thats not working properly and ExtJS handles the number input anyway correctly
        return $ret;
    }

    protected function _getTrlProperties()
    {
        $ret = parent::_getTrlProperties();
        $ret[] = 'decimalSeparator';
        return $ret;
    }

    public function trlStaticExecute($language = null)
    {
        parent::trlStaticExecute($language);
        if (!$language) {
            $language = Kwf_Trl::getInstance()->getTargetLanguage();
        }
        $this->_floatValidator->setLocale($language);
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
            $this->addValidator(new Kwf_Validate_Int());
        } else {
            $this->addValidator($this->_floatValidator);
        }
    }

    protected function _getValueToSaveFromPostData($postData)
    {
        $ret = parent::_getValueToSaveFromPostData($postData);
        if ($this->getAllowDecimals()) {
            $l = $this->_floatValidator->getLocale();
            $ret = Zend_Locale_Format::getNumber($ret, array('locale' => $l));
        }
        return $ret;
    }

    protected function _getValueFromPostData($postData)
    {
        $fieldName = $this->getFieldName();
        if (!isset($postData[$fieldName])) $postData[$fieldName] = null;
        if ($postData[$fieldName] === '') {
            $postData[$fieldName] = null;
        }
        $ret = $postData[$fieldName];
        if ($ret &&
            !(isset($postData[$fieldName.'-format']) && $postData[$fieldName.'-format'] == 'fe') &&
            $this->getAllowDecimals() !== false
        ) {
            //ext always sends as 123.23 which we can parse using (float)$ret
            $ret = number_format((float)$ret, $this->getDecimalPrecision(), $this->getDecimalSeparator(), '');
        }
        return $ret;
    }

    protected function _getInputProperties($values, $fieldNamePostfix, $idPrefix)
    {
        $ret = parent::_getInputProperties($values, $fieldNamePostfix, $idPrefix);
        if ($this->getMaxValue()) {
            $ret['max'] = $this->getMaxValue();
        }
        if ($this->getMinValue()) {
            $ret['min'] = $this->getMinValue();
        }
        if ($this->getAllowNegative() === false) {
            if (!isset($ret['min']) || $ret['min'] > 0) {
                $ret['min'] = 0;
            }
        }
        if ($this->getAllowDecimals() === false) {
            $ret['step'] = '1';
        } else {
            //browser support for type number with decimals is broken, don't use it
            $ret['type'] = 'text';
            //$ret['step'] = 'any';
            unset($ret['max']);
            unset($ret['min']);
            $ret['pattern'] = '\d*('.preg_quote($this->getDecimalSeparator()).'\d*)?'; //instead of type=number; will however NOT show number keyboard on iPad
        }
        return $ret;

    }

    public function getTemplateVars($values, $fieldNamePostfix = '', $idPrefix = '')
    {
        $ret = parent::getTemplateVars($values, $fieldNamePostfix, $idPrefix);
        //add additional hidden input field for frontend forms so we know the posted value is from frontend and formatted like current locale
        $ret['html'] .= "\n<input type=\"hidden\" name=\"".$this->getFieldName().$fieldNamePostfix."-format\" value=\"fe\" />";
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
