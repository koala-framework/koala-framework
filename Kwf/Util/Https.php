<?php
class Kwf_Util_Https
{
    static $supportsHttps; //set in setup

    public static function ensureHttps()
    {
        if (php_sapi_name() != 'cli' && self::supportsHttps()) {
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

    /**
     * Returns if the current request would support https and ensureHttps() would redirect to https
     */
    public static function supportsHttps()
    {
        return self::$supportsHttps;
    }

    /**
     * Returns if the current request would support https and ensureHttps() would redirect to https
     */
    public static function domainSupportsHttps($domain)
    {
        if (Kwf_Config::getValue('server.https')) {
            if ($domains = Kwf_Config::getValueArray('server.httpsDomains')) {
                if ($domains && !in_array($domain, $domains)) {
                    return false; //current host is not in server.httpsDomains, don't use https
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Returns if the given component requests https
     *
     * Return value is cached.
     */
    public static function doesComponentRequestHttps(Kwf_Component_Data $data)
    {
        $showInvisible = Kwf_Component_Data_Root::getShowInvisible();

        $foundRequestHttps = false;
        if (!$showInvisible) { //don't cache in preview
            $cacheId = 'reqHttps-'.$data->componentId;
            $foundRequestHttps = Kwf_Cache_Simple::fetch($cacheId);
        }

        if ($foundRequestHttps === false) {
            $foundRequestHttps = 0; //don't use false, false means not-cached
            if (Kwf_Component_Abstract::getFlag($data->componentClass, 'requestHttps')) {
                $foundRequestHttps = true;
            }
            if (!$foundRequestHttps && $data->getRecursiveChildComponents(array(
                    'page' => false,
                    'flags' => array('requestHttps' => true)
                ))
            ) {
                $foundRequestHttps = true;
            }
            if (!$showInvisible) { //don't cache in preview
                Kwf_Cache_Simple::add($cacheId, $foundRequestHttps);
            }
        }

        return $foundRequestHttps;
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
