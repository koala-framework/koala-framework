<?php
/**
 * A simple and fast cache. Doesn't have all the Zend_Cache bloat.
 */
class Vps_Cache_Simple
{
    public static function fetch($cacheId, &$success = true)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix().'-';
        return apc_fetch($prefix.$cacheId, $success);
    }

    public static function add($cacheId, $data)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix().'-';
        return apc_add($prefix.$cacheId, $data);
    }

    public static function delete($cacheId)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix().'-';
        return apc_delete($prefix.$cacheId);
    }

    public static function clear($cacheIdPrefix)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix().'-';
        apc_delete_file(new APCIterator('user', '#^'.preg_quote($prefix.$cacheIdPrefix).'#'));
    }
}
