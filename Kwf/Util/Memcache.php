<?php
class Kwf_Util_Memcache
{
    /**
     * wrapper around server.memcache.host setting with support for %webserverHostname%
     */
    public static function getHost()
    {
        static $ret;
        if (!isset($ret)) {
            $ret = Kwf_Config::getValue('server.memcache.host');
            if ($ret == '%webserverHostname%') {
                if (php_sapi_name() == 'cli') {
                    $ret = Kwf_Util_Apc::callUtil('get-hostname', array(), array('returnBody'=>true, 'skipCache'=>true));
                } else {
                    $ret = php_uname('n');
                }
            }
        }
        return $ret;
    }

    /**
     * wrapper around server.memcache.port setting
     */
    public static function getPort()
    {
        return Kwf_Config::getValue('server.memcache.port');
    }
}
