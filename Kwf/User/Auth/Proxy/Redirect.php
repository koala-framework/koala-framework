<?php
class Kwf_User_Auth_Proxy_Redirect extends Kwf_User_Auth_Proxy_Abstract implements Kwf_User_Auth_Interface_Redirect
{
    public function showInFrontend()
    {
        return $this->_auth->showInFrontend();
    }

    public function showInBackend()
    {
        return $this->_auth->showInBackend();
    }

    public function getLoginRedirectLabel()
    {
        return $this->_auth->getLoginRedirectLabel();
    }

    public function getLoginRedirectFormOptions()
    {
        return $this->_auth->getLoginRedirectFormOptions();
    }

    public function getLoginRedirectUrl($redirectBackUrl, $state, $formValues)
    {
        return $this->_auth->getLoginRedirectUrl($redirectBackUrl, $state, $formValues);
    }

    public function getUserToLoginByParams(array $params)
    {
        $row = $this->_auth->getUserToLoginByParams($params);
        if (!$row) return null;
        return $this->_model->getRowByProxiedRow($row);
    }

    public function getUserToLoginByCallbackParams($redirectBackUrl, array $params)
    {
        $row = $this->_auth->getUserToLoginByCallbackParams($redirectBackUrl, $params);
        if (!$row) return null;
        return $this->_model->getRowByProxiedRow($row);
    }

    public function associateUserByCallbackParams(Kwf_Model_Row_Interface $user, $redirectBackUrl, array $params)
    {
        $this->_auth->associateUserByCallbackParams($user->getProxiedRow(), $redirectBackUrl, $params);
    }

    public function allowPasswordForUser(Kwf_Model_Row_Interface $user)
    {
        return $this->_auth->allowPasswordForUser($user->getProxiedRow());
    }
}
