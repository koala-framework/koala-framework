<?php
class Vps_Form_Field_NumberField extends Vps_Form_Field_TextField
{
    public function __construct($field_name = null, $field_label = null)
    {
        parent::__construct($field_name, $field_label);
        $this->setXtype('numberfield');
        $this->setDecimalSeparator(trlcVps('decimal separator', '.'));
        $this->setDecimalPrecision(2);
    }
    protected function _addValidators()
    {
        parent::_addValidators();

        if ($this->getAllowDecimals() === false) {
            $this->addValidator(new Vps_Validate_Digits());
        } else {
            $l = null;
            if (trlcVps('locale', 'C') != 'C') {
                $l = Zend_Locale::findLocale(trlcVps('locale', 'C'));
            }
            $this->addValidator(new Zend_Validate_Float($l));
        }
        if ($this->getMaxValue()) {
            $this->addValidator(new Vps_Validate_MaxValue($this->getMaxValue()));
        }
        if ($this->getMinValue()) {
            $this->addValidator(new Vps_Validate_MinValue($this->getMinValue()));
        }
        if ($this->getAllowNegative() === false) {
            $this->addValidator(new Zend_Validate_GreaterThan(-1));
        }
    }

    protected function _getValueFromPostData($postData)
    {
        $fieldName = $this->getFieldName();
        if (!isset($postData[$fieldName])) $postData[$fieldName] = null;
        if ($postData[$fieldName] == ''
            && !(is_int($postData[$fieldName]) && $postData[$fieldName] === 0)
        ) {
            $postData[$fieldName] = null;
        }
        if (!is_null($postData[$fieldName])) {
            if ($this->getDecimalSeparator() != '.') {
                $postData[$fieldName] = str_replace($this->getDecimalSeparator(), '.', $postData[$fieldName]);
            }
        }
        return $postData[$fieldName];
    }

    protected function _getOutputValueFromValues($values)
    {
        $ret = parent::_getOutputValueFromValues($values);
        $ret = number_format($ret, $this->getDecimalPrecision(), $this->getDecimalSeparator(), '');
        return $ret;
    }

    public static function getSettings()
    {
        return array_merge(parent::getSettings(), array(
            'componentName' => trlVps('Number Field')
        ));
    }
}
