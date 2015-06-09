<?php
class Kwf_User_Auth_Union_Abstract extends Kwf_User_Auth_Abstract
{
    protected $_auth;
    protected $_model;

    public function __construct(Kwf_User_Auth_Abstract $auth, Kwf_Model_Union $model)
    {
        $this->_auth = $auth;
        $this->_model = $model;
    }

    public function getInnerAuth()
    {
        return $this->_auth;
    }
}
