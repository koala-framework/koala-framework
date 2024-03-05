<?php
/**
 * Cache for view cache used in front of database
 *
 * Will NOT get deleted on clear-cache
 */
class Kwf_Component_Cache_Memory
{
    private static $_zendCache = null;
    private static $_instance;
    const CACHE_VERSION = 1; //increase when incompatible changes to cache contents are made, additionally cache_component table needs to be truncated by update script


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
        if (strlen($cacheId) > 50) {
            $cacheId = substr($cacheId, 0, 50).md5($cacheId);
        }
        return "cache/view/".self::CACHE_VERSION.'-'.$cacheId;
    }

    public function loadWithMetaData($id)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcached') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-'.self::CACHE_VERSION.'-';

            $tmp = Kwf_Cache_Simple::getMemcached()->get($prefix.$id);
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
        } else if ($be == 'memcache') {
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
            $ret = self::getZendCache()->loadWithMetaData($id);
            if (substr($ret['contents'], 0, 13) == 'kwf-timestamp') {
                return substr($ret['contents'], 13);
            } else {
                return $ret;
            }
        }
    }

    public function save($data, $id, $ttl)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcached') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-'.self::CACHE_VERSION.'-';
            $data = array($data, time(), $ttl);
            return Kwf_Cache_Simple::getMemcached()->set($prefix.$id, $data, $ttl);
        } else if ($be == 'memcache') {
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

    public function remove($id, $microtime)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcached') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-'.self::CACHE_VERSION.'-';
            return Kwf_Cache_Simple::getMemcached()->set($prefix.$id, $microtime);
        } else if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-'.self::CACHE_VERSION.'-';
            return Kwf_Cache_Simple::getMemcache()->set($prefix.$id, $microtime);
        } else if ($be == 'file') {
            $file = self::_getFileNameForCacheId($id);
            if (!file_exists($file)) return false;
            unlink($file);
            return true;
        } else {
            return self::getZendCache()->save('kwf-timestamp' . $microtime, $id);
        }
    }

    /**
     * Internal function only ment to be used by unit tests
     *
     * @internal
     */
    public function _clean()
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcached') {
            return Kwf_Cache_Simple::getMemcached()->flush();
        } else if ($be == 'memcache') {
            return Kwf_Cache_Simple::getMemcache()->flush();
        } else if ($be == 'file') {
            foreach (glob('cache/view/*') as $i) {
                unlink($i);
            }
            return true;
        } else {
            return self::getZendCache()->clean();
        }
    }

}
