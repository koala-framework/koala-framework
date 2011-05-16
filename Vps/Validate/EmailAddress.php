<?php
class Vps_Validate_EmailAddress extends Zend_Validate_EmailAddress
{
    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->_messageTemplates[self::INVALID] = trlVpsStatic("'%value%' is not a valid email address");
        $this->_messageTemplates[self::INVALID_HOSTNAME] = trlVpsStatic("'%hostname%' is not a valid hostname for email address '%value%'");
        $this->_messageTemplates[self::INVALID_MX_RECORD] = trlVpsStatic("'%hostname%' does not appear to have a valid MX record for the email address '%value%'");
        $this->_messageTemplates[self::DOT_ATOM] = trlVpsStatic("'%localPart%' not matched against dot-atom format");
        $this->_messageTemplates[self::QUOTED_STRING] = trlVpsStatic("'%localPart%' not matched against quoted-string format");
        $this->_messageTemplates[self::INVALID_LOCAL_PART] = trlVpsStatic("'%localPart%' is not a valid local part for email address '%value%'");
    }

    public function setHostnameValidator(Zend_Validate_Hostname $hostnameValidator = null, $allow = Zend_Validate_Hostname::ALLOW_DNS)
    {
        if ($hostnameValidator === null) {
            $hostnameValidator = new Vps_Validate_Hostname($allow);
        }
        return parent::setHostnameValidator($hostnameValidator);
    }
}
