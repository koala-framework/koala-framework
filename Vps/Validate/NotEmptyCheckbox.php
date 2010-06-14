<?php
class Vps_Validate_NotEmptyCheckbox extends Zend_Validate_NotEmpty
{
    public function __construct()
    {
        $this->_messageTemplates[self::IS_EMPTY] = trlVpsStatic("Please mark the checkbox");
    }

    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);
        if (empty($value) || $value == '0') {
            $this->_error();
            return false;
        }

        return true;
    }
}
