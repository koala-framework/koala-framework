<?php
class Kwf_User_Auth_Proxy_AutoLogin extends Kwf_User_Auth_Proxy_Abstract
{
    public function getRowById($id)
    {
        $row = $this->_auth->getRowById($id);
        if (!$row) return null;
        return $this->_model->getRowByProxiedRow($row);
    }

    public function generateAutoLoginToken(Kwf_Model_Row_Interface $row)
    {
        return $this->_auth->generateAutoLoginToken($row->getProxiedRow());
    }

    public function clearAutoLoginToken(Kwf_Model_Row_Interface $row)
    {
        return $this->_auth->clearAutoLoginToken($row->getProxiedRow());
    }

    public function validateAutoLoginToken(Kwf_Model_Row_Interface $row, $token)
    {
        return $this->_auth->validateAutoLoginToken($row->getProxiedRow(), $token);
    }
}
