<?php
abstract class Kwf_Component_Cache
{
    static private $_instance;
    static private $_backend = self::CACHE_BACKEND_MYSQL;
    const CACHE_BACKEND_MYSQL = 'Kwf_Component_Cache_Mysql';
    const CACHE_BACKEND_FNF = 'Kwf_Component_Cache_Fnf';
    const NO_CACHE = '{nocache}';

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

    public abstract function deleteViewCache($select);

    public function writeBuffer()
    {
        foreach ($this->_models as $m) {
            if (is_object($m)) $m->writeBuffer();
        }
    }
}
