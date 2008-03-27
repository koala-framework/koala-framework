<?php

require_once 'Zend/Auth/Adapter/Interface.php';

class Vps_Auth_Adapter_Service implements Zend_Auth_Adapter_Interface
{
    protected $_identity = null;
    protected $_credential = null;

    protected $_userId = null;

    public function setIdentity($identd)
    {
        $this->_identity = $identd;
        return $this;
    }

    public function setCredential($credential)
    {
        $this->_credential = $credential;
        return $this;
    }

    public function getUserId()
    {
        return $this->_userId;
    }

    public function authenticate()
    {
        if (empty($this->_identity)) {
            throw new Vps_Exception('A value for the identity was not provided prior to authentication with Vps_Auth_Adapter_Service.');
        } else if ($this->_credential === null) {
            throw new Vps_Exception('A credential value was not provided prior to authentication with Vps_Auth_Adapter_Service.');
        }

        $users = Zend_Registry::get('userModel');
        $result = $users->login($this->_identity, $this->_credential);

        if (isset($result['userId'])) {
            $this->_userId = $result['userId'];
        }

        return new Zend_Auth_Result(
            $result['zendAuthResultCode'], $result['identity'], $result['messages']
        );
    }

}