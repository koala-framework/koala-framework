<?php
class Kwf_Validate_NotEmptyNotZero extends Kwf_Validate_NotEmpty
{
    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);
        if (empty($value)) {
            $this->_error(self::IS_EMPTY);
            return false;
        }
        return true;
    }
}
