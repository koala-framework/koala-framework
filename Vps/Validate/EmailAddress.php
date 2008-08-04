<?php
class Vps_Validate_EmailAddress extends Zend_Validate_EmailAddress
{
    public function __construct($allow = Zend_Validate_Hostname::ALLOW_DNS, $validateMx = false, Zend_Validate_Hostname $hostnameValidator = null)
    {
        parent::__construct($allow, $validateMx, $hostnameValidator);
        $this->_messageTemplates[self::INVALID] = trlVps("'%value%' is not a valid email address in the basic format local-part@hostname");
        $this->_messageTemplates[self::INVALID_HOSTNAME] = trlVps("'%hostname%' is not a valid hostname for email address '%value%'");
        $this->_messageTemplates[self::INVALID_MX_RECORD] = trlVps("'%hostname%' does not appear to have a valid MX record for the email address '%value%'");
        $this->_messageTemplates[self::DOT_ATOM] = trlVps("'%localPart%' not matched against dot-atom format");
        $this->_messageTemplates[self::QUOTED_STRING] = trlVps("'%localPart%' not matched against quoted-string format");
        $this->_messageTemplates[self::INVALID_LOCAL_PART] = trlVps("'%localPart%' is not a valid local part for email address '%value%'");
    }
}
