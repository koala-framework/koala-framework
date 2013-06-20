<?php
class Kwf_Util_Https
{
    public static function ensureHttps()
    {
        if (php_sapi_name() != 'cli' && Kwf_Registry::get('config')->server->https) {
            if (!isset($_SERVER['HTTPS']) && $_SERVER['REQUEST_METHOD'] != 'POST') {
                $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
                header('Location: '.$redirect, true, 302);
                Kwf_Benchmark::shutDown();
                exit;
            }
        }
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
