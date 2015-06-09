<?php
class Kwf_User_Auth_Proxy_Abstract extends Kwf_User_Auth_Abstract
{
    protected $_auth;
    protected $_model;

    public function __construct(Kwf_User_Auth_Abstract $auth, Kwf_Model_Proxy $model)
    {
        $this->_auth = $auth;
        $this->_model = $model;
    }

    public function getInnerAuth()
    {
        return $this->_auth;
    }
}
