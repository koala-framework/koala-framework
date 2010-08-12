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
            $this->_error(self::IS_EMPTY);
            return false;
        }

        return true;
    }
}
