<?php
interface Kwf_User_Auth_Interface_Redirect
{
    public function getLoginRedirectLabel();
    public function getLoginRedirectUrl($redirectBackUrl);
    public function getUserToLoginByParams(array $params);
    public function createSampleLoginLinks($absoluteUrl);
}
