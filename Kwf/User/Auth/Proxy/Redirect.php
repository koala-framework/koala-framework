<?php
class Kwf_User_Auth_Proxy_Redirect extends Kwf_User_Auth_Proxy_Abstract implements Kwf_User_Auth_Interface_Redirect
{
    public function getLoginRedirectLabel()
    {
        return $this->_auth->getLoginRedirectLabel();
    }

    public function getLoginRedirectUrl($redirectBackUrl, $state)
    {
        return $this->_auth->getLoginRedirectUrl($redirectBackUrl, $state);
    }

    public function getUserToLoginByParams($redirectBackUrl, array $params)
    {
        $row = $this->_auth->getUserToLoginByParams($redirectBackUrl, $params);
        if (!$row) return null;
        return $this->_model->getRowByProxiedRow($row);
    }

    public function associateUserByParams(Kwf_Model_Row_Interface $user, $redirectBackUrl, array $params)
    {
        $this->_auth->associateUserByParams($user->getProxiedRow(), $redirectBackUrl, $params);
    }

    public function createSampleLoginLinks($absoluteUrl)
    {
        return $this->_auth->createSampleLoginLinks($absoluteUrl);
    }
}
