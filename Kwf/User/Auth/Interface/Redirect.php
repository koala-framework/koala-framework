<?php
interface Kwf_User_Auth_Interface_Redirect
{
    public function getLoginRedirectLabel();
    public function getLoginRedirectUrl($redirectBackUrl, $state);
    public function getUserToLoginByParams($redirectBackUrl, array $params);
    public function associateUserByParams(Kwf_Model_Row_Interface $user, $redirectBackUrl, array $params);
    public function createSampleLoginLinks($absoluteUrl);
}
