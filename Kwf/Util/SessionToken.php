<?php
//Session token to catch CSRF attacks
class Kwf_Util_SessionToken
{
    public static function getSessionToken()
    {
        if (!Kwf_Setup::hasAuthedUser()) return null;
        $ns = new Kwf_Session_Namespace('sessionToken');
        if (!$ns->token) {
            $ns->token = md5(microtime().mt_rand());
        }
        return $ns->token;
    }
}
