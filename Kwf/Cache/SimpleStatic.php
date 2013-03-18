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
    private static function _getZendCache()
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new Zend_Cache_Core(array(
                'lifetime' => null,
                'write_control' => false,
                'automatic_cleaning_factor' => 0,
                'automatic_serialization' => true
            ));
            if (extension_loaded('memcache')) {
                $cache->setBackend(new Kwf_Cache_Backend_Memcached());
            } else {
                //fallback to file backend (NOT recommended!)
                $cache->setBackend(new Kwf_Cache_Backend_File(array(
                    'cache_dir' => 'cache/simple'
                )));
            }
        }
        return $cache;
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
     * Delete static cache, don't use except in unittests
     *
     * @internal
     */
    public static function _delete($cacheIds)
    {
        if (!is_array($cacheIds)) $cacheIds = array($cacheIds);

        $cache = self::_getZendCache();
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
            $result = Kwf_Util_Apc::callClearCacheByCli(array('cacheIds' => implode(',', $ids)));
            if (!$result['result']) $ret = false;
        }
        return $ret;
    }
}
