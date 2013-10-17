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

    public function loadWithMetaData($id)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-'.self::CACHE_VERSION.'-';

            $tmp = Kwf_Cache_Simple::getMemcache()->get($prefix.$id);
            if (is_array($tmp) && array_key_exists(0, $tmp)) {
                return array(
                    'contents' => $tmp[0],
                    'timestamp' => $tmp[1],
                    'expire' => $tmp[2] ? ($tmp[1] + $tmp[2]) : null, //mtime + lifetime
                );
            } else if ($tmp) {
                return $tmp;
            }
            return false;
        } else {
            $ret = self::getZendCache()->loadWithMetaData($id);
            if (substr($ret['contents'], 0, 13) == 'kwf-timestamp') {
                return $ret['contents'];
            } else {
                return $ret;
            }
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
        } else {
            return self::getZendCache()->save($data, $id, array(), $ttl);
        }

    }

    public function remove($id, $microtime)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-'.self::CACHE_VERSION.'-';
            return Kwf_Cache_Simple::getMemcache()->set($prefix.$id, $microtime);
        } else {
            return self::getZendCache()->save('kwf-timestamp' . $microtime, $id);
        }
    }

}
