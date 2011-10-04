<?php
class Vps_Validate_NotEmptyNotZero extends Vps_Validate_NotEmpty
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
