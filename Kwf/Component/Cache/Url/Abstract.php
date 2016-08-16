<?php
abstract class Kwf_Component_Cache_Url_Abstract
{
    static private $_instance;
    static private $_backend = self::CACHE_BACKEND_MYSQL;
    const CACHE_BACKEND_MYSQL = 'Kwf_Component_Cache_Url_Mysql';
    const CACHE_BACKEND_REDIS = 'Kwf_Component_Cache_Url_Redis';
    const CACHE_BACKEND_FNF = 'Kwf_Component_Cache_Url_Fnf';

    /**
     * @return Kwf_Component_Cache_Mysql
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            $backend = self::$_backend;
            self::$_instance = new $backend();
        }
        return self::$_instance;
    }

    public static function setInstance($backend)
    {
        self::clearInstance();
        self::$_backend = $backend;
    }

    public static function clearInstance()
    {
        self::$_instance = null;
    }

    public abstract function load($cacheUrl);
    public abstract function save($cacheUrl, Kwf_Component_Data $data);
    public abstract function delete(array $constraints);
    public abstract function clear();

}
