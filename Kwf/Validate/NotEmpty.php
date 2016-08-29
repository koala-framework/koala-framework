<?php
/**
 * empty, aber 0 ist erlaubt
 *
 * @package Validate
 */
class Kwf_Validate_NotEmpty extends Zend_Validate_NotEmpty
{
    public function __construct()
    {
        $this->_messageTemplates[self::IS_EMPTY] = trlKwfStatic("Please fill out the field");
    }

    public function setMessage($type, $msg = null)
    {
        $this->_messageTemplates[$type] = $msg;
    }

    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);
        if (empty($value) && $value != '0') {
            $this->_error(self::IS_EMPTY);
            return false;
        }

        return true;
    }
}
