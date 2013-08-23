<?php
/**
 * Cache for view cache used in front of database
 *
 * If aws.simpleCacheCluster used it will NOT get deleted on clear-cache
 */
class Kwf_Component_Cache_Memory
{
    private static $_zendCache = null;

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
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';

            $tmp = Kwf_Cache_Simple::getMemcache()->get($prefix.$id);
            if (is_array($tmp) && isset($tmp[0])) {
                return array(
                    'contents' => $tmp[0],
                    'expire' => $tmp[2] ? ($tmp[1] + $tmp[2]) : null, //mtime + lifetime
                );
            }
            return false;
        } else {
            return self::getZendCache()->loadWithMetaData($id);
        }
    }

    public function save($data, $id, $ttl)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            $data = array($data, time(), $ttl);
            return Kwf_Cache_Simple::getMemcache()->set($prefix.$id, $data, MEMCACHE_COMPRESSED, $ttl);
        } else {
            return self::getZendCache()->save($data, $id, array(), $ttl);
        }

    }

    public function remove($id)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            return Kwf_Cache_Simple::getMemcache()->delete($prefix.$id);
        } else {
            return self::getZendCache()->remove($id);
        }
    }

}
