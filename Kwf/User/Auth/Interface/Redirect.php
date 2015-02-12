<?php
interface Kwf_User_Auth_Interface_Redirect
{
    public function showInFrontend();
    public function showInBackend();
    public function getLoginRedirectLabel();
    public function getLoginRedirectFormOptions();
    public function getLoginRedirectUrl($redirectBackUrl, $state, $formValues);
    public function getUserToLoginByParams($redirectBackUrl, array $params);
    public function associateUserByParams(Kwf_Model_Row_Interface $user, $redirectBackUrl, array $params);
    public function createSampleLoginLinks($absoluteUrl);
    public function allowPasswordForUser(Kwf_Model_Row_Interface $user);
}
