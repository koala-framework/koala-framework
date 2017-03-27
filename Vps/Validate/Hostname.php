<?php
class Vps_Validate_Hostname extends Zend_Validate_Hostname
{
    public function __construct($allow = self::ALLOW_DNS, $validateIdn = true, $validateTld = false, Zend_Validate_Ip $ipValidator = null)
    {
        $this->_messageTemplates[self::IP_ADDRESS_NOT_ALLOWED] = trlVps("'%value%' appears to be an IP address, but IP addresses are not allowed");
        $this->_messageTemplates[self::UNKNOWN_TLD] = trlVps("'%value%' appears to be a DNS hostname but cannot match TLD against known list");
        $this->_messageTemplates[self::INVALID_DASH] = trlVps("'%value%' appears to be a DNS hostname but contains a dash (-) in an invalid position");
        $this->_messageTemplates[self::INVALID_HOSTNAME_SCHEMA] = trlVps("'%value%' appears to be a DNS hostname but cannot match against hostname schema for TLD '%tld%'");
        $this->_messageTemplates[self::UNDECIPHERABLE_TLD] = trlVps("'%value%' appears to be a DNS hostname but cannot extract TLD part");
        $this->_messageTemplates[self::INVALID_HOSTNAME] = trlVps("'%value%' does not match the expected structure for a DNS hostname");
        $this->_messageTemplates[self::INVALID_LOCAL_NAME] = trlVps("'%value%' does not appear to be a valid local network name");
        $this->_messageTemplates[self::LOCAL_NAME_NOT_ALLOWED] = trlVps("'%value%' appears to be a local network name but local network names are not allowed");
        parent::__construct($allow, $validateIdn, $validateTld, $ipValidator);
     }
}
