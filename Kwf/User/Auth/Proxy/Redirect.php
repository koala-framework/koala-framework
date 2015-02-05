<?php
class Kwf_User_Auth_Proxy_Redirect extends Kwf_User_Auth_Proxy_Abstract implements Kwf_User_Auth_Interface_Redirect
{
    public function getLoginRedirectLabel()
    {
        return $this->_auth->getLoginRedirectLabel();
    }

    public function getLoginRedirectUrl($redirectBackUrl)
    {
        return $this->_auth->getLoginRedirectUrl($redirectBackUrl);
    }

    public function getUserToLoginByParams(array $params)
    {
        $row = $this->_auth->getUserToLoginByParams($params);
        if (!$row) return null;
        return $this->_model->getRowByProxiedRow($row);
    }

    public function createSampleLoginLinks($absoluteUrl)
    {
        return $this->_auth->createSampleLoginLinks($absoluteUrl);
    }
}
