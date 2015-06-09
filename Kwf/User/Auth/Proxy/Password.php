<?php
class Kwf_User_Auth_Proxy_Password extends Kwf_User_Auth_Proxy_Abstract implements Kwf_User_Auth_Interface_Password
{
    public function getRowByIdentity($identity)
    {
        $row = $this->_auth->getRowByIdentity($identity);
        if (!$row) return null;
        return $this->_model->getRowByProxiedRow($row);
    }

    public function validatePassword(Kwf_Model_Row_Interface $row, $password)
    {
        return $this->_auth->validatePassword($row->getProxiedRow(), $password);
    }

    public function setPassword(Kwf_Model_Row_Interface $row, $password)
    {
        return $this->_auth->setPassword($row->getProxiedRow(), $password);
    }

    public function sendLostPasswordMail(Kwf_Model_Row_Interface $row, Kwf_User_Row $kwfUserRow)
    {
        return $this->_auth->sendLostPasswordMail($row->getProxiedRow(), $kwfUserRow);
    }
}
