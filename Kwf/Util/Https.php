<?php
class Kwf_Util_Https
{
    /**
     * Returns if the current request is https
     */
    public static function supportsHttps()
    {
        return isset($_SERVER['HTTPS']);
    }

    /**
     * Returns if the current request would support https and ensureHttps() would redirect to https
     */
    public static function domainSupportsHttps($domain)
    {
        if (Kwf_Config::getValue('server.https') === true) {
            if ($domains = Kwf_Config::getValueArray('server.httpsDomains')) {
                if ($domains && !in_array($domain, $domains)) {
                    foreach ($domains as $d) {
                        if (substr($d, 0, 2) == '*.') {
                            if (substr($d, 1) == substr($domain, strpos($domain, '.'))) {
                                return true;
                            }
                        }
                    }
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
