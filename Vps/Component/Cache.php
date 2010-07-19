<?php
class Vps_Component_Cache
{
    static private $_instance;
    static private $_backend = self::CACHE_BACKEND_MYSQL;
    const CACHE_BACKEND_MYSQL = 'Vps_Component_Cache_Mysql';
    const CACHE_BACKEND_MONGO = 'Vps_Component_Cache_Mongo';
    const CACHE_BACKEND_FNF = 'Vps_Component_Cache_Fnf';

    public static function getInstance()
    {
        if (!self::$_instance) {
            $backend = self::$_backend;
            self::$_instance = new $backend();
        }
        return self::$_instance;
    }

    public static function setBackend($backend)
    {
        self::$_backend = $backend;
    }

    public static function clearInstance()
    {
        self::$_instance = null;
    }
}
