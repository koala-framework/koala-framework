<?php
class Kwf_Auth_Storage_Session extends Zend_Auth_Storage_Session
{
    public function __construct($namespace = self::NAMESPACE_DEFAULT, $member = self::MEMBER_DEFAULT)
    {
        $this->_namespace = $namespace;
        $this->_member    = $member;
        $this->_session   = new Kwf_Session_Namespace($this->_namespace);
    }
}
