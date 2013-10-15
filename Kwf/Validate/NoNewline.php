<?php
class Kwf_Validate_NoNewline extends Zend_Validate_Abstract
{
    const INVALID_NEWLINE = 'invalidNewline';

    public function __construct()
    {
        $this->_messageTemplates[self::INVALID_NEWLINE] = trlKwfStatic("Must not include newlines");
    }

    public function isValid($value)
    {
        if (strpos($value, "\n") !== false || strpos($value, "\r") !== false) {
            $this->_error(self::INVALID_NEWLINE);
            return false;
        }
        return true;
    }
}
