<?php
/**
 * Cache for media output
 *
 * Will NOT get deleted on clear-cache
 */
class Kwf_Media_MemoryCache
{
    /**
     * @var Zend_Cache_Core
     */
    private $_secondLevelCache = null;
    /**
     * @var self
     */
    private static $_instance;


    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function setInstance($instance)
    {
        self::$_instance = $instance;
    }

    private function _getSecondLevelCache()
    {
        if (!$this->_secondLevelCache) {
            $c = new Zend_Cache_Core(array(
                'lifetime' => null,
                'write_control' => false,
                'automatic_cleaning_factor' => 0,
                'automatic_serialization' => true,
            ));
            $c->setBackend(new Kwf_Cache_Backend_File(array(
                'cache_dir' => 'cache/mediameta',
                'hashed_directory_level' => 2,
            )));
            $this->_secondLevelCache = $c;
        }
        return $this->_secondLevelCache;
    }

    private static function _processCacheId($cacheId)
    {
        return preg_replace('#[^a-zA-Z0-9_]#', '_', $cacheId);
    }

    public function load($id)
    {
        $id = self::_processCacheId($id);
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            $ret = Kwf_Cache_Simple::getMemcache()->get($prefix.$id);
        } else if ($be == 'file') {
            //use secondlevel cache only
            $ret = false;
        } else {
            $ret = Kwf_Cache_Simple::fetch('media-'.$id);
        }
        if ($ret === false) {
            $ret = $this->_getSecondLevelCache()->load($id);
            if ($ret && $be != 'file') {
                //first level empty, refill from second level contents
                $metaDatas = $this->_getSecondLevelCache()->getMetadatas($id);
                $ttl = $metaDatas['expire'] ? $metaDatas['expire']-$metaDatas['mtime'] : 0;
                if ($be == 'memcache') {
                    $flags = is_int($ret) ? null : MEMCACHE_COMPRESSED;
                    Kwf_Cache_Simple::getMemcache()->set($prefix.$id, $ret, $flags, $ttl);
                } else {
                    Kwf_Cache_Simple::add('media-'.$id, $ret, $ttl);
                }
            }
        }
        return $ret;
    }

    public function save($data, $id, $ttl = null)
    {
        $id = self::_processCacheId($id);
        $this->_getSecondLevelCache()->save($data, $id, array(), $ttl);
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            $flags = is_int($data) ? null : MEMCACHE_COMPRESSED;
            return Kwf_Cache_Simple::getMemcache()->set($prefix.$id, $data, $flags, $ttl);
        } else if ($be == 'file') {
            //use secondlevel cache only
            return true;
        } else {
            return Kwf_Cache_Simple::add('media-'.$id, $data, $ttl);
        }

    }

    public function remove($id)
    {
        $id = self::_processCacheId($id);
        $this->_getSecondLevelCache()->remove($id);
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            return Kwf_Cache_Simple::getMemcache()->delete($prefix.$id);
        } else if ($be == 'file') {
            return true;
        } else {
            return Kwf_Cache_Simple::delete('media-'.$id);
        }
    }

    public function clean()
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            foreach ($this->_getSecondLevelCache()->getIds() as $id) {
                Kwf_Cache_Simple::getMemcache()->delete($prefix.$id);
            }
        } else if ($be == 'file') {
            //use secondlevel cache only
        } else {
            foreach ($this->_getSecondLevelCache()->getIds() as $id) {
                Kwf_Cache_Simple::delete('media-'.$id);
            }
        }
        $this->_getSecondLevelCache()->clean();
    }

}
