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

        if (Kwf_Config::getValue('aws.simpleCacheCluster')) {
            $this->setBackend(new Kwf_Util_Aws_ElastiCache_CacheBackend(array(
                'cacheClusterId' => Kwf_Config::getValue('aws.simpleCacheCluster'),
                'compression' => true,
            )));
            //do *not* use cache_namespace for this cache (we don't want to delete it on clear-cache)
        } else {
            if (extension_loaded('apc')) {
                $this->setBackend(new Kwf_Cache_Backend_Apc());
            } else {
                if (extension_loaded('memcache')) {
                    $this->setBackend(new Kwf_Cache_Backend_Memcached(array(
                        'compression' => true,
                    )));
                } else {
                    //fallback to file backend (NOT recommended!)
                    $this->setBackend(new Kwf_Cache_Backend_File(array(
                        'cache_dir' => 'cache/view',
                        'hashed_directory_level' => 2,
                    )));
                }
            }
        }
    }
}
