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
    private static function _processId($cacheId)
    {
        $cacheId = str_replace('-', '__', $cacheId);
        $cacheId = preg_replace('#[^a-zA-Z0-9_]#', '_', $cacheId);
        return $cacheId;
    }

    //for 'file' backend
    private static function _getFileNameForCacheId($cacheId)
    {
        $cacheId = preg_replace('#[^a-zA-Z0-9_-]#', '_', $cacheId);
        return "cache/simple/".$cacheId;
    }

    public static function fetch($cacheId, &$success = true)
    {
        static $prefix;
        if (extension_loaded('apc')) {
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            return apc_fetch($prefix.$cacheId, $success);
        } else {
            $file = self::_getFileNameForCacheId($cacheId);
            if (!file_exists($file)) {
                $success =  false;
                return false;
            }
            $success = true;
            return unserialize(file_get_contents($file));
        }
    }

    public static function add($cacheId, $data)
    {
        static $prefix;
        if (extension_loaded('apc')) {
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            return apc_add($prefix.$cacheId, $data);
        } else {
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
        if (extension_loaded('apc')) {
            if (php_sapi_name() == 'cli') {
                Kwf_Util_Apc::callClearCacheByCli(array('clearCacheSimpleStatic'=>array($cacheIdPrefix)));
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
                        apc_clear_cache('user');
                    } else {
                        //APCIterator seems to work, use it for deletion
                        apc_delete($it);
                    }
                }
            }
        } else {
            foreach (glob(self::_getFileNameForCacheId($cacheIdPrefix).'*') as $f) {
                unlink($f);
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
        if (!extension_loaded('apc')) {
            foreach ($cacheIds as $cacheId) {
                $file = self::_getFileNameForCacheId($cacheId);
                if (!file_exists($file)) {
                    $ret = false;
                } else {
                    if (!unlink($file)) $ret = false;
                }
            }
        } else {
            $prefix = Kwf_Cache_Simple::getUniquePrefix().'-';
            $ids = array();
            foreach ($cacheIds as $cacheId) {
                if (!apc_delete($prefix.$cacheId)) {
                    $ret = false;
                }
                $ids[] = $prefix.$cacheId;
            }
            if (php_sapi_name() == 'cli' && $ids) {
                $result = Kwf_Util_Apc::callClearCacheByCli(array('cacheIds' => implode(',', $ids)));
                if (!$result['result']) $ret = false;
            }
        }
        return $ret;
    }
}
