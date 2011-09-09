<?php
/**
 * A simple and fast cache. Doesn't have all the Zend_Cache bloat.
 *
 * If available it uses apc user cache direclty (highly recommended!!), else it falls
 * back to Zend_Cache using a memcache backend.
 */
class Vps_Cache_Simple
{
    private static function _getCache()
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new Zend_Cache_Core(array(
                //'backend' => new Vps_Cache_Backend_Memcached(),
                'lifetime' => null,
                'write_control' => false,
                'automatic_cleaning_factor' => 0,
                'automatic_serialization' => true
            ));
            $cache->setBackend(new Vps_Cache_Backend_Memcached());
        }
        return $cache;
    }

    private static function _processId($cacheId)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
        $cacheId = str_replace('-', '__', $prefix.$cacheId);
        $cacheId = preg_replace('#[^a-zA-Z0-9_]#', '_', $cacheId);
        return $cacheId;
    }

    public static function fetch($cacheId, &$success = true)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
        if (extension_loaded('apc')) {
            return apc_fetch($prefix.$cacheId, $success);
        } else {
            $ret = self::_getCache()->load(self::_processId($cacheId));
            $success = $ret !== false;
            return $ret;
        }
    }

    public static function add($cacheId, $data)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
        if (extension_loaded('apc')) {
            return apc_add($prefix.$cacheId, $data);
        } else {
            return self::_getCache()->save($data, self::_processId($cacheId));
        }
    }

    public static function delete($cacheId)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
        if (extension_loaded('apc')) {
            return apc_delete($prefix.$cacheId);
        } else {
            return self::_getCache()->remove(self::_processId($cacheId));
        }
    }

    public static function clear($cacheIdPrefix)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
        if (extension_loaded('apc')) {
            apc_delete_file(new APCIterator('user', '#^'.preg_quote($prefix.$cacheIdPrefix).'#'));
        } else {
            //we can't do any better here :/
            self::_getCache()->clean();
        }
    }

    public static function getUniquePrefix()
    {
        static $ret;
        if (!isset($ret)) {
            $ret = getcwd().'-'.Vps_Setup::getConfigSection().'-';
        }
        return $ret;
    }
}
