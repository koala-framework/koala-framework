<?php
class Vps_Util_FeedFetcher_Feed_Cache extends Vps_Cache_Core
{
    static $_instance;
    public function __construct()
    {
        $options = array();
        $options['lifetime'] = null;
        $options['automatic_serialization'] = true;
        $options['checkComponentSettings'] = false;
        $options['automatic_cleaning_factor'] = 0;
        parent::__construct($options);

        $backend = new Vps_Cache_Backend_TwoLevels(array(
            'cache_dir' => 'application/cache/feeds',
        ));
        $this->setBackend($backend);
    }

    public static function clearInstance()
    {
        self::$_instance = null;
    }

    public static function getInstance()
    {
        if (!isset(self::$_instance)) self::$_instance = new self();
        return self::$_instance;
    }

    public function load($cacheId, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        $ret = parent::load($cacheId, $doNotTestCacheValidity, $doNotUnserialize);
        if (!isset($ret['entries'])) $ret = false;
        return $ret;
    }
}
