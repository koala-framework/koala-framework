<?php
interface Kwf_User_Auth_Interface_Redirect
{
    public function getLoginRedirectUrl($redirectBackUrl);
    public function loginByRedirectBackParams($params);
}
