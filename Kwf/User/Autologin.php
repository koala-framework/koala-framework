<?php
class Kwf_User_Autologin
{
    public static function processCookies()
    {
        if (isset($_COOKIE['feAutologin']) && !Kwf_Auth::getInstance()->getStorage()->read()) {
            $feAutologin = explode('.', $_COOKIE['feAutologin']);
            if (count($feAutologin) == 2) {
                $adapter = new Kwf_Auth_Adapter_PasswordAuth();
                $adapter->setIdentity($feAutologin[0]);
                $adapter->setCredential($feAutologin[1]);
                $adapter->setUseCookieToken(true);
                $auth = Kwf_Auth::getInstance();
                $auth->clearIdentity();
                $result = $auth->authenticate($adapter);
                if (!$result->isValid()) {
                    self::clearCookies();
                }
            }
        }
    }

    public static function clearToken($authedUser)
    {
        $authedUser->clearAutoLoginToken();
    }

    public static function clearCookies()
    {
        setcookie('feAutologin', '', time() - 3600, '/', null, isset($_SERVER['HTTPS']), true);
    }

    public static function setCookies($authedUser)
    {
        $cookieValue = $authedUser->id.'.'.$authedUser->generateAutoLoginToken();
        setcookie('feAutologin', $cookieValue, time() + (100*24*60*60), '/', null, isset($_SERVER['HTTPS']), true);
    }
}
