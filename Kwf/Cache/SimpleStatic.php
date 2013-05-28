<?php
/**
 * A simple and fast cache that can't delete individual entries. Doesn't have all the Zend_Cache bloat.
 *
 * Use for values depending on static settings that won't change except code changes.
 *
 * If available it uses apc user cache direclty (highly recommended!!), else it falls
 * back to Zend_Cache using a memcache backend.
 */
class Kwf_Cache_SimpleStatic
{
    private static $_zendCache = null;

    public static function resetZendCache()
    {
        self::$_zendCache = null;
    }

    private static function _getZendCache()
    {
        if (!isset(self::$_zendCache)) {
            self::$_zendCache = new Zend_Cache_Core(array(
                'lifetime' => null,
                'write_control' => false,
                'automatic_cleaning_factor' => 0,
                'automatic_serialization' => true
            ));
            if (extension_loaded('memcache')) {
                self::$_zendCache->setBackend(new Kwf_Cache_Backend_Memcached());
            } else {
                //fallback to file backend (NOT recommended!)
                self::$_zendCache->setBackend(new Kwf_Cache_Backend_File(array(
                    'cache_dir' => 'cache/simple'
                )));
            }
        }
        return self::$_zendCache;
    }

    private static function _processId($cacheId)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
        $cacheId = str_replace('-', '__', $prefix.$cacheId);
        $cacheId = preg_replace('#[^a-zA-Z0-9_]#', '_', $cacheId);
        return $cacheId;
    }

    public static function fetch($cacheId, &$success = true)
    {
        static $prefix;
        if (extension_loaded('apc')) {
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            return apc_fetch($prefix.$cacheId, $success);
        } else {
            $ret = self::_getZendCache()->load(self::_processId($cacheId));
            $success = $ret !== false;
            return $ret;
        }
    }

    public static function add($cacheId, $data, $ttl = null)
    {
        static $prefix;
        if (extension_loaded('apc')) {
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            return apc_add($prefix.$cacheId, $data, $ttl);
        } else {
            return self::_getZendCache()->save($data, self::_processId($cacheId), array(), $ttl);
        }
    }

    /**
     * clear static cache with prefix, don't use except in clear-cache-watcher
     *
     * @internal
     */
    public static function clear($cacheIdPrefix)
    {
        if (extension_loaded('apc')) {
            if (!class_exists('APCIterator')) {
                throw new Kwf_Exception_NotYetImplemented("We don't want to clear the whole");
            } else {
                static $prefix;
                if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
                $it = new APCIterator('user', '#^'.preg_quote($prefix.$cacheIdPrefix).'#', APC_ITER_NONE);
                if ($it->getTotalCount() && !$it->current()) {
                    //APCIterator is borked, delete everything
                    //see https://bugs.php.net/bug.php?id=59938
                    apc_clear_cache('user');
                } else {
                    //APCIterator seems to work, use it for deletion
                    apc_delete($it);
                }
            }
        } else {
            throw new Kwf_Exception_NotYetImplemented("We don't want to clear the whole");
        }
    }

    /**
     * Delete static cache, don't use except in unittests
     *
     * @internal
     */
    public static function _delete($cacheIds)
    {
        if (!is_array($cacheIds)) $cacheIds = array($cacheIds);

        if (extension_loaded('apc')) {
            $cache = false;
        } else {
            $cache = self::_getZendCache();
        }
        $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
        $ret = true;
        $ids = array();
        foreach ($cacheIds as $cacheId) {
            if (!$cache) {
                $r = apc_delete($prefix.$cacheId);
                $ids[] = $prefix.$cacheId;
            } else {
                $r = $cache->remove(self::_processId($cacheId));
            }
            if (!$r) $ret = false;
        }
        if (!$cache && php_sapi_name() == 'cli' && $ids) {
            $result = Kwf_Util_Apc::callClearCacheByCli(array('cacheIds' => implode(',', $ids)), Kwf_Util_Apc::SILENT);
            if (!$result['result']) $ret = false;
        }
        return $ret;
    }
}
