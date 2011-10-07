<?php
class Kwf_Validate_EmailAddress extends Zend_Validate_EmailAddress
{
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->_messageTemplates[self::INVALID] = trlKwfStatic("'%value%' is not a valid email address");
        $this->_messageTemplates[self::INVALID_HOSTNAME] = trlKwfStatic("'%hostname%' is not a valid hostname for email address '%value%'");
        $this->_messageTemplates[self::INVALID_MX_RECORD] = trlKwfStatic("'%hostname%' does not appear to have a valid MX record for the email address '%value%'");
        $this->_messageTemplates[self::DOT_ATOM] = trlKwfStatic("'%localPart%' not matched against dot-atom format");
        $this->_messageTemplates[self::QUOTED_STRING] = trlKwfStatic("'%localPart%' not matched against quoted-string format");
        $this->_messageTemplates[self::INVALID_LOCAL_PART] = trlKwfStatic("'%localPart%' is not a valid local part for email address '%value%'");
    }

    public function setHostnameValidator(Zend_Validate_Hostname $hostnameValidator = null, $allow = Zend_Validate_Hostname::ALLOW_DNS)
    {
        if ($hostnameValidator === null) {
            $hostnameValidator = new Kwf_Validate_Hostname($allow);
        }
        return parent::setHostnameValidator($hostnameValidator);
    }
}
