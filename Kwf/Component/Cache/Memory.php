<?php
/**
 * Cache for view cache used in front of database
 *
 * If aws.simpleCacheCluster used it will NOT get deleted on clear-cache
 */
class Kwf_Component_Cache_Memory
{
    private static $_zendCache = null;
    const CACHE_VERSION = 1; //increase when incompatible changes to cache contents are made, additionally cache_component table needs to be truncated by update script

    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            $i = new self();
        }
        return $i;
    }

    public static function getZendCache()
    {
        if (!isset(self::$_zendCache)) {
            self::$_zendCache = new Kwf_Component_Cache_MemoryZend();
        }
        return self::$_zendCache;
    }

    //for 'file' backend
    private static function _getFileNameForCacheId($cacheId)
    {
        $cacheId = preg_replace('#[^a-zA-Z0-9_-]#', '_', $cacheId);
        return "cache/view/".self::CACHE_VERSION.'-'.$cacheId;
    }

    public function loadWithMetaData($id)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-'.self::CACHE_VERSION.'-';

            $tmp = Kwf_Cache_Simple::getMemcache()->get($prefix.$id);
            if (is_array($tmp) && isset($tmp[0])) {
                return array(
                    'contents' => $tmp[0],
                    'expire' => $tmp[2] ? ($tmp[1] + $tmp[2]) : null, //mtime + lifetime
                );
            }
            return false;
        } else if ($be == 'file') {
            $file = self::_getFileNameForCacheId($id);
            if (!file_exists($file)) return false;
            $data = unserialize(file_get_contents($file));
            if ($data['expire'] && time() > $data['expire']) {
                unlink($file);
                return false;
            }
            return $data;
        } else {
            return self::getZendCache()->loadWithMetaData($id);
        }
    }

    public function save($data, $id, $ttl)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-'.self::CACHE_VERSION.'-';
            $data = array($data, time(), $ttl);
            return Kwf_Cache_Simple::getMemcache()->set($prefix.$id, $data, MEMCACHE_COMPRESSED, $ttl);
        } else if ($be == 'file') {
            $file = self::_getFileNameForCacheId($id);
            $data = array(
                'contents' => $data,
                'expire' => $ttl ? time()+$ttl : null
            );
            return file_put_contents($file, serialize($data));
        } else {
            return self::getZendCache()->save($data, $id, array(), $ttl);
        }

    }

    public function remove($id)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-'.self::CACHE_VERSION.'-';
            return Kwf_Cache_Simple::getMemcache()->delete($prefix.$id);
        } else if ($be == 'file') {
            $file = self::_getFileNameForCacheId($id);
            if (!file_exists($file)) return false;
            return unlink($file);
        } else {
            return self::getZendCache()->remove($id);
        }
    }

}
