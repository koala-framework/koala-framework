<?php
class Kwf_Validate_PostCode extends Zend_Validate_PostCode
{
    // $options has to set as locale string for example for Austria PostCode set "de_AT"
    public function __construct($options = null)
    {
        $this->_messageTemplates[self::INVALID] = trlKwfStatic("Invalid type given. The value should be a string or a integer");
        $this->_messageTemplates[self::NO_MATCH] = trlKwfStatic("'%value%' does not appear to be a postal code");
        parent::__construct($options);
    }
}
