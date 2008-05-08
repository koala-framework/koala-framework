<?php
class Vps_Component_Cache  {
    
    static private $_instance;
    
    public function __construct()
    {
        $frontendOptions = array(
            'lifetime' => 30*60
        );
        $backendOptions = array(
            'cache_dir' => 'application/cache/component',
            'hashed_directory_level' => 2,
            'file_name_prefix' => 'vpc',
            'hashed_directory_umask' => 0770
        );
        $this->_cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
    }
    
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    public function getCache()
    {
        return $this->_cache;
    }
    
    public function cleanByTag($tag)
    {
        $this->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array($tag));
    }
    
    public function remove($componentId)
    {
        $this->getCache()->remove($this->getCacheIdFromComponentId($componentId));
    }
    
    public function getCacheIdFromComponentId($componentId)
    {
        return str_replace('-', '__', $componentId);
    }
    
    public function getComponentIdFromCacheId($cacheId)
    {
        return str_replace('__', '-', $cacheId);
    }
}