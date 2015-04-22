<?php
class Kwf_User_Auth_Proxy_Activation extends Kwf_User_Auth_Proxy_Abstract implements Kwf_User_Auth_Interface_Activation
{
    public function validateActivationToken(Kwf_Model_Row_Interface $row, $token)
    {
        return $this->_auth->validateActivationToken($row->getProxiedRow(), $token);
    }

    public function generateActivationToken(Kwf_Model_Row_Interface $row, $type)
    {
        return $this->_auth->generateActivationToken($row->getProxiedRow(), $type);
    }

    public function isActivated(Kwf_Model_Row_Interface $row)
    {
        return $this->_auth->isActivated($row->getProxiedRow());
    }

    public function clearActivationToken(Kwf_Model_Row_Interface $row)
    {
        return $this->_auth->clearActivationToken($row->getProxiedRow());
    }
}
