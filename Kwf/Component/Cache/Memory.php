<?php
/**
 * Cache for view cache used in front of database
 *
 * If aws.simpleCacheCluster used it will NOT get deleted on clear-cache
 */
class Kwf_Component_Cache_Memory extends Zend_Cache_Core
{
    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            $i = new self();
        }
        return $i;
    }

    public function __construct()
    {
        $options = array(
            'lifetime' => null,
            'write_control' => false,
            'automatic_cleaning_factor' => 0,
            'automatic_serialization' => true,
        );
        parent::__construct($options);

        $be = Kwf_Cache_Simple::getBackend();

        if ($be == 'elastiCache') {
            $this->setBackend(new Kwf_Util_Aws_ElastiCache_CacheBackend(array(
                'cacheClusterId' => Kwf_Config::getValue('aws.simpleCacheCluster'),
                'compression' => true,
            )));
            //do *not* use cache_namespace for this cache (we don't want to delete it on clear-cache)
        } else if ($be == 'apcu') {
            $this->setBackend(new Kwf_Cache_Backend_Apcu());
        } else if ($be == 'memcache') {
            $this->setBackend(new Kwf_Cache_Backend_Memcached(array(
                'compression' => true,
            )));
            //do *not* use cache_namespace for this cache (we don't want to delete it on clear-cache)
        } else if ($be == 'apc') {
            $this->setBackend(new Kwf_Cache_Backend_Apc());
        } else {
            //fallback to file backend (NOT recommended!)
            $this->setBackend(new Kwf_Cache_Backend_File(array(
                'cache_dir' => 'cache/view',
                'hashed_directory_level' => 2,
            )));
        }
    }

    public function loadWithMetaData($id, $doNotTestCacheValidity = false, $doNotUnserialize = false)
    {
        if (!$this->_options['caching']) {
            return false;
        }
        $id = $this->_id($id); // cache id may need prefix
        self::_validateIdOrTag($id);
        $data = $this->_backend->loadWithMetaData($id, $doNotTestCacheValidity);
        if ($data===false) {
            // no cache available
            return false;
        }
        if ((!$doNotUnserialize) && $this->_options['automatic_serialization']) {
            // we need to unserialize before sending the result
            $data['contents'] = unserialize($data['contents']);
        }
        return $data;
    }
}
