<?php
class Kwf_Util_Memcache
{
    /**
     * wrapper around server.memcache.host setting with support for %webserverHostname%
     */
    public static function getHost()
    {
        return Kwf_Cache_Simple::$memcacheHost;
    }

    /**
     * wrapper around server.memcache.port setting
     */
    public static function getPort()
    {
        return Kwf_Cache_Simple::$memcachePort;
    }
}
