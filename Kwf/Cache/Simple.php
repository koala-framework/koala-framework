<?php
/**
 * A simple and fast cache. Doesn't have all the Zend_Cache bloat.
 *
 * If available it uses apc user cache or memcache directly (highly recommended!!), else it falls
 * back to Zend_Cache using a (slow) file backend.
 *
 * If aws.simpleCacheCluster is set Aws ElastiCache will be used.
 */
class Kwf_Cache_Simple
{
    public static $backend; //set in Setup
    public static $memcacheHost; //set in Setup
    public static $memcachePort; //set in Setup

    private static $_zendCache = null;
    private static $_cacheNamespace = null;

    public static function resetZendCache()
    {
        self::$_zendCache = null;
        self::$_cacheNamespace = null;
    }

    public static function getBackend()
    {
        if (isset(self::$backend)) {
            return self::$backend;
        }
        if (Kwf_Config::getValue('aws.simpleCacheCluster')) {
            $ret = 'elastiCache';
        } else if (Kwf_Config::getValue('server.memcache.host')) {
            $ret = 'memcache';
        } else if (extension_loaded('apc') && !Kwf_Config::getValue('server.apcStaticOnly')) {
            $ret = 'apc';
        } else {
            $ret = 'file';
        }
        return $ret;
    }

    public static function getZendCache()
    {
        if (!isset(self::$_zendCache)) {
            $be = self::getBackend();
            if ($be == 'elastiCache') {
                //TODO: use similar like memcache without Zend_Cache
                self::$_zendCache = new Zend_Cache_Core(array(
                    'lifetime' => null,
                    'write_control' => false,
                    'automatic_cleaning_factor' => 0,
                    'automatic_serialization' => true
                ));
                self::$_zendCache->setBackend(new Kwf_Util_Aws_ElastiCache_CacheBackend(array(
                    'cacheClusterId' => Kwf_Config::getValue('aws.simpleCacheCluster'),
                )));
            } else if ($be == 'apc') {
                self::$_zendCache = false;
            } else {
                self::$_zendCache = new Zend_Cache_Core(array(
                    'lifetime' => null,
                    'write_control' => false,
                    'automatic_cleaning_factor' => 0,
                    'automatic_serialization' => true
                ));
                if ($be == 'memcache') {
                    //not used using Zend_Cache
                    self::$_zendCache = false;
                } else {
                    self::$_zendCache = false;
                }
            }

            if (self::$_zendCache) {
                $be = self::$_zendCache->getBackend();
                if ($be instanceof Zend_Cache_Backend_Memcached) {
                    //namespace is incremented in Kwf_Util_ClearCache
                    //use memcache directly as Zend would not save the integer directly and we can't increment it then
                    $v = self::$_zendCache->getBackend()->getMemcache()->get(self::getUniquePrefix().'cache_namespace');
                    if (!$v) {
                        $v = time();
                        self::$_zendCache->getBackend()->getMemcache()->set(self::getUniquePrefix().'cache_namespace', $v);
                    }
                    if ($be instanceof Kwf_Util_Aws_ElastiCache_CacheBackend) {
                        //Kwf_Util_Aws_ElastiCache_CacheBackend doesn't use Kwf_Cache_Backend_Memcached, so we don't have a app prefix
                        //set app prefix ourselves
                        $v = Kwf_Config::getValue('application.id').Kwf_Setup::getConfigSection().$v;
                    }
                    self::$_zendCache->setOption('cache_id_prefix', $v);
                }
            }
        }
        return self::$_zendCache;
    }

    private static function _processId($cacheId)
    {
        $cacheId = str_replace('-', '__', $cacheId);
        $cacheId = preg_replace('#[^a-zA-Z0-9_]#', '_', $cacheId);
        return $cacheId;
    }

    public static function getMemcache()
    {
        static $memcache;
        if (isset($memcache)) return $memcache;
        $memcache = new Memcache;
        $memcache->addServer(self::$memcacheHost, self::$memcachePort);
        return $memcache;
    }

    private static function _getMemcachePrefix()
    {
        if (!isset(self::$_cacheNamespace)) {
            $mc = self::getMemcache();
            //namespace is incremented in Kwf_Util_ClearCache
            //use memcache directly as Zend would not save the integer directly and we can't increment it then
            $v = $mc->get(self::getUniquePrefix().'cache_namespace');
            if (!$v) {
                $v = time();
                $mc->set(self::getUniquePrefix().'cache_namespace', $v);
            }
            self::$_cacheNamespace = self::getUniquePrefix().'-'.$v;
        }
        return self::$_cacheNamespace;
    }

    //for 'file' backend
    public static function _getFileNameForCacheId($cacheId)
    {
        $cacheId = preg_replace('#[^a-zA-Z0-9_-]#', '_', $cacheId);
        return "cache/simple/".$cacheId;
    }

    public static function fetch($cacheId, &$success = true)
    {
        if (self::getBackend() == 'memcache') {
            $ret = self::getMemcache()->get(self::_getMemcachePrefix().$cacheId);
            $success = $ret !== false;
            return $ret;
        } else if (self::getBackend() == 'apc') {
            static $prefix;
            if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
            return apc_fetch($prefix.$cacheId, $success);
        } else if (self::getBackend() == 'file') {
            $file = self::_getFileNameForCacheId($cacheId);
            if (!file_exists($file)) {
                $success =  false;
                return false;
            }
            $data = unserialize(file_get_contents($file));
            if (isset($data[1]) && time() > $data[1]) {
                //expired
                unlink($file);
                $success =  false;
                return false;
            }
            $success = true;
            return $data[0];
        } else {
            if (!isset(self::$_zendCache)) self::getZendCache();
            $ret = self::$_zendCache->load(self::_processId($cacheId));
            $success = $ret !== false;
            return $ret;
        }
    }

    public static function add($cacheId, $data, $ttl = null)
    {
        if (self::getBackend() == 'memcache') {
            return self::getMemcache()->set(self::_getMemcachePrefix().$cacheId, $data, 0, $ttl);
        } else if (self::getBackend() == 'apc') {
            static $prefix;
            if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
            return apc_add($prefix.$cacheId, $data, $ttl);
        } else if (self::getBackend() == 'file') {
             $file = self::_getFileNameForCacheId($cacheId);
             $data = array($data);
             if ($ttl) $data[1] = time()+$ttl;
             return file_put_contents($file, serialize($data));
        } else {
            if (!isset(self::$_zendCache)) self::getZendCache();
            return self::$_zendCache->save($data, self::_processId($cacheId), array(), $ttl);
        }
    }

    public static function delete($cacheIds)
    {
        if (!is_array($cacheIds)) $cacheIds = array($cacheIds);
        $ret = true;
        $ids = array();
        foreach ($cacheIds as $cacheId) {
            if (self::getBackend() == 'memcache') {
                $r = self::getMemcache()->delete(self::_getMemcachePrefix().$cacheId);
            } else if (self::getBackend() == 'apc') {
                static $prefix;
                if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
                $r = apc_delete($prefix.$cacheId);
                $ids[] = $prefix.$cacheId;
            } else if (self::getBackend() == 'file') {
                $r = true;
                $file = self::_getFileNameForCacheId($cacheId);
                if (!file_exists($file)) {
                    $r = false;
                } else {
                    if (!unlink($file)) $r = false;
                }
            } else {
                if (!isset(self::$_zendCache)) self::getZendCache();
                $r = self::$_zendCache->remove(self::_processId($cacheId));
            }
            if (!$r) $ret = false;
        }
        if (self::getBackend() == 'apc' && php_sapi_name() == 'cli' && $ids) {
            $ret = Kwf_Util_Apc::callClearCacheByCli(array('cacheIds' => implode(',', $ids)));
        }
        return $ret;
    }

    public static function clear($cacheIdPrefix)
    {
        throw new Kwf_Exception("don't delete the whole cache");
    }

    public static function getUniquePrefix()
    {
        static $ret;
        if (!isset($ret)) {
            $ret = getcwd().'-'.Kwf_Setup::getConfigSection().'-';
        }
        return $ret;
    }
}
