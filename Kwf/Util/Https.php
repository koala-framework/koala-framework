<?php
class Kwf_Util_Https
{
    public static function ensureHttps()
    {
        if (self::supportsHttps()) {
            if (!isset($_SERVER['HTTPS']) && $_SERVER['REQUEST_METHOD'] != 'POST') {
                $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                header('Location: '.$redirect, true, 302);
                Kwf_Benchmark::shutDown();
                exit;
            }
        }
    }

    public static function ensureHttp()
    {
        if (php_sapi_name() != 'cli') {
            if (isset($_SERVER['HTTPS']) && $_SERVER['REQUEST_METHOD'] != 'POST' && !Kwf_Session::sessionExists()) {
                $redirect = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                header('Location: '.$redirect, true, 302);
                Kwf_Benchmark::shutDown();
                exit;
            }
        }
    }

    public static function supportsHttps()
    {
        if (php_sapi_name() != 'cli' && Kwf_Config::getValue('server.https')) {
            if ($domains = Kwf_Config::getValueArray('server.httpsDomains')) {
                if ($domains && !in_array($_SERVER['HTTP_HOST'], $domains)) {
                    return false; //current host is not in server.httpsDomains, don't use https
                }
            }
            return true;
        }
        return false;
    }

    /**
     * IE unter <=XP kann kein SNI
    private static function _supportsHttps()
    {
        if (!isset($_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
        //               IE                    2000, XP, 98, 95
        if (preg_match('#MSIE [^;]+; Windows (NT (4|5)|98|95)#', $_SERVER['HTTP_USER_AGENT'])) {
            return false;
        }

        return true;
    }
    */
}
