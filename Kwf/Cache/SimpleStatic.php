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
    private static $_cache = array(); //only used when apc is not used (and also on cli)
    private static $_fileCacheDisabled = false;

    private static function _processId($cacheId)
    {
        return str_replace(array('/', '+', '='), array('_', '__', ''), base64_encode($cacheId));
    }

    //for 'file' backend
    private static function _getFileNameForCacheId($cacheId)
    {
        $cacheId = str_replace('/', '_', base64_encode($cacheId));
        if (strlen($cacheId) > 50) {
            $cacheId = substr($cacheId, 0, 50).md5($cacheId);
        }
        return "cache/simpleStatic/".$cacheId;
    }

    public static function fetch($cacheId, &$success = true)
    {
        static $prefix;
        static $extensionLoaded;
        if (!isset($extensionLoaded)) $extensionLoaded = extension_loaded('apc');
        if ($extensionLoaded && PHP_SAPI != 'cli') {
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            return apc_fetch($prefix.$cacheId, $success);
        } else {
            if (isset(self::$_cache[$cacheId])) {
                $success = true;
                return self::$_cache[$cacheId];
            }
            if (self::$_fileCacheDisabled) {
                $success =  false;
                return false;
            }
            $file = self::_getFileNameForCacheId($cacheId);
            if (!file_exists($file)) {
                $success =  false;
                return false;
            }
            $success = true;
            $ret = unserialize(file_get_contents($file));
            self::$_cache[$cacheId] = $ret;
            return $ret;
        }
    }

    public static function add($cacheId, $data)
    {
        static $prefix;
        static $extensionLoaded;
        if (!isset($extensionLoaded)) $extensionLoaded = extension_loaded('apc');
        if ($extensionLoaded && PHP_SAPI != 'cli') {
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            return apc_add($prefix.$cacheId, $data);
        } else {
            self::$_cache[$cacheId] = $data;
            if (self::$_fileCacheDisabled) {
                return true;
            }
            $file = self::_getFileNameForCacheId($cacheId);
            return file_put_contents($file, serialize($data));
        }
    }

    /**
     * clear static cache with prefix, don't use except in clear-cache-watcher
     *
     * @internal
     */
    public static function clear($cacheIdPrefix)
    {
        if (!extension_loaded('apc') || PHP_SAPI == 'cli') {
            self::$_cache = array();
            //don't use $cacheIdPrefix as filenames are base64 encoded
            foreach (glob('cache/simpleStatic/*') as $f) {
                unlink($f);
            }
            if (extension_loaded('apc')) {
                Kwf_Util_Apc::callClearCacheByCli(array('clearCacheSimpleStatic'=>$cacheIdPrefix));
            }
        } else {
            if (!class_exists('APCIterator')) {
                throw new Kwf_Exception_NotYetImplemented("We don't want to clear the whole");
            } else {
                static $prefix;
                if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
                $it = new APCIterator('user', '#^'.preg_quote($prefix.$cacheIdPrefix).'#', APC_ITER_NONE);
                if ($it->getTotalCount() && !$it->current()) {
                    //APCIterator is borked, delete everything
                    //see https://bugs.php.net/bug.php?id=59938
                    if (extension_loaded('apcu')) {
                        apc_clear_cache();
                    } else {
                        apc_clear_cache('user');
                    }
                } else {
                    //APCIterator seems to work, use it for deletion
                    apc_delete($it);
                }
            }
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

        $ret = true;
        if (!extension_loaded('apc') || PHP_SAPI == 'cli') {
            foreach ($cacheIds as $cacheId) {
                unset(self::$_cache[$cacheId]);
                $file = self::_getFileNameForCacheId($cacheId);
                if (!file_exists($file)) {
                    $ret = false;
                } else {
                    if (!unlink($file)) $ret = false;
                }
            }
            if (extension_loaded('apc')) {
                $result = Kwf_Util_Apc::callClearCacheByCli(array('clearCacheSimpleStatic' => implode(',', $cacheIds)));
                if (!$result['result']) $ret = false;
            }
        } else {
            $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            foreach ($cacheIds as $cacheId) {
                if (!apc_delete($prefix.$cacheId)) {
                    $ret = false;
                }
            }
        }
        return $ret;
    }

    /**
     * Disables the cache/simple file based cache
     */
    public static function disableFileCache()
    {
        self::$_fileCacheDisabled = true;
    }
}

