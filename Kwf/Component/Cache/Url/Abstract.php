<?php
abstract class Kwf_Component_Cache_Url_Abstract
{
    static private $_instance;

    /**
     * @return Kwf_Component_Cache_Url_Mysql
     */
    public static function getInstance()
    {
        if (!self::$_instance) {
            if (Kwf_Cache_Simple::$redisHost) {
                self::$_instance = new Kwf_Component_Cache_Url_Redis;
            } else {
                self::$_instance = new Kwf_Component_Cache_Url_Mysql;
            }
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

    public function collectGarbage($debug)
    {
    }
}
