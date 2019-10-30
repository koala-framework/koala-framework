<?php
/**
 * A simple and fast cache. Doesn't have all the Zend_Cache bloat.
 *
 * If available it uses apc user cache or memcache directly (highly recommended!!), else it falls
 * back to Zend_Cache using a (slow) file backend.
 *
 */
class Kwf_Cache_Simple
{
    public static $backend; //set in Setup
    public static $uniquePrefix; //set in Setup
    public static $namespace; //set in Setup
    public static $memcacheHost; //set in Setup
    public static $memcachePort; //set in Setup
    public static $redisHost; //set in Setup
    public static $redisPort; //set in Setup

    private static $_zendCache = null;

    public static function resetZendCache()
    {
        self::$_zendCache = null;
        self::$namespace = null;
    }

    public static function getBackend()
    {
        if (isset(self::$backend)) {
            return self::$backend;
        }
        if (Kwf_Config::getValue('server.memcache.host')) {
            //prefer memcache over redis
            if (extension_loaded('memcached')) {
                $ret = 'memcached';
            } else {
                $ret = 'memcache';
            }
        } else if (Kwf_Config::getValue('server.redis.host')) {
            $ret = 'redis';
        } else if (extension_loaded('apcu') && !Kwf_Config::getValue('server.apcStaticOnly')) {
            $ret = 'apcu';
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
            if ($be == 'apc' || $be == 'apcu') {
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
                if ($be instanceof Zend_Cache_Backend_Libmemcached) {
                    self::$_zendCache->setOption('cache_id_prefix', self::_getMemcachedPrefix());
                } else if ($be instanceof Zend_Cache_Backend_Memcached) {
                    self::$_zendCache->setOption('cache_id_prefix', self::_getMemcachePrefix());
                }
            }
        }
        return self::$_zendCache;
    }

    private static function _processId($cacheId)
    {
        return str_replace(array('/', '+', '='), array('_', '__', ''), base64_encode($cacheId));
    }

    public static function getMemcache()
    {
        static $memcache;
        if (isset($memcache)) return $memcache;
        if (!self::$memcacheHost) {
            return false;
        }
        $memcache = new Memcache;
        $memcache->addServer(self::$memcacheHost, self::$memcachePort);
        return $memcache;
    }

    public static function getMemcached()
    {
        static $memcached;
        if (isset($memcached)) return $memcached;
        if (!self::$memcacheHost) {
            return false;
        }
        $memcached = new Memcached;
        $memcached->addServer(self::$memcacheHost, self::$memcachePort);
        return $memcached;
    }

    public static function getRedis()
    {
        static $redis;
        if (isset($redis)) return $redis;
        if (!self::$redisHost) {
            return false;
        }
        $redis = new Redis();
        $redis->connect(self::$redisHost, self::$redisPort);
        $redis->setOption(Redis::OPT_PREFIX, self::$uniquePrefix);
        $redis->setOption(Redis::OPT_SCAN, Redis::SCAN_RETRY);
        return $redis;
    }

    private static function _getMemcachePrefix()
    {
        if (!isset(self::$namespace)) {
            $mc = self::getMemcache();
            //namespace is incremented in Kwf_Util_ClearCache
            //use memcache directly as Zend would not save the integer directly and we can't increment it then
            $v = $mc->get(self::$uniquePrefix.'cache_namespace');
            if (!$v) {
                $v = time();
                $mc->set(self::$uniquePrefix.'cache_namespace', $v);
            }
            self::$namespace = self::$uniquePrefix.'-'.$v;
        }
        return self::$namespace;
    }

    private static function _getMemcachedPrefix()
    {
        if (!isset(self::$namespace)) {
            $mc = self::getMemcached();
            //namespace is incremented in Kwf_Util_ClearCache
            //use memcache directly as Zend would not save the integer directly and we can't increment it then
            $v = $mc->get(self::$uniquePrefix.'cache_namespace');
            if (!$v) {
                $v = time();
                $mc->set(self::$uniquePrefix.'cache_namespace', $v);
            }
            self::$namespace = self::$uniquePrefix.'-'.$v;
        }
        return self::$namespace;
    }

    //for 'file' backend
    public static function _getFileNameForCacheId($cacheId)
    {
        $cacheId = str_replace('/', '_', base64_encode($cacheId));
        if (strlen($cacheId) > 50) {
            $cacheId = substr($cacheId, 0, 50).md5($cacheId);
        }
        return "cache/simple/".$cacheId;
    }

    public static function fetch($cacheId, &$success = true)
    {
        if (self::getBackend() == 'memcached') {
            $ret = self::getMemcached()->get(self::_getMemcachedPrefix().md5($cacheId));
            $success = $ret !== false;
            return $ret;
        } else if (self::getBackend() == 'memcache') {
            $ret = self::getMemcache()->get(self::_getMemcachePrefix().md5($cacheId));
            $success = $ret !== false;
            return $ret;
        } else if (self::getBackend() == 'redis') {
            $ret = self::getRedis()->get('simple:'.self::$namespace.':'.$cacheId);
            $success = $ret !== false;
            if ($success) {
                $ret = unserialize($ret);
            }
            return $ret;
        } else if (self::getBackend() == 'apc') {
            static $prefix;
            if (!isset($prefix)) $prefix = self::$uniquePrefix.'-';
            return apc_fetch($prefix.$cacheId, $success);
        } else if (self::getBackend() == 'apcu') {
            static $prefix;
            if (!isset($prefix)) $prefix = self::$uniquePrefix.'-';
            return apcu_fetch($prefix.$cacheId, $success);
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
        if (self::getBackend() == 'memcached') {
            return self::getMemcached()->set(self::_getMemcachedPrefix().md5($cacheId), $data, $ttl);
        } else if (self::getBackend() == 'memcache') {
            return self::getMemcache()->set(self::_getMemcachePrefix().md5($cacheId), $data, 0, $ttl);
        } else if (self::getBackend() == 'redis') {
            if (!$ttl) $ttl = 365*24*60*60; //Set a TTL so it can be evicted http://stackoverflow.com/questions/16370278/how-to-make-redis-choose-lru-eviction-policy-for-only-some-of-the-keys
            $ret = self::getRedis()->setEx('simple:'.self::$namespace.':'.$cacheId, $ttl, serialize($data));
            return $ret;
        } else if (self::getBackend() == 'apc') {
            static $prefix;
            if (!isset($prefix)) $prefix = self::$uniquePrefix.'-';
            return apc_add($prefix.$cacheId, $data, $ttl);
        } else if (self::getBackend() == 'apcu') {
            static $prefix;
            if (!isset($prefix)) $prefix = self::$uniquePrefix.'-';
            return apcu_add($prefix.$cacheId, $data, $ttl);
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

        if (self::getBackend() == 'redis') {
            foreach ($cacheIds as &$id) {
                $id = 'simple:'.self::$namespace.':'.$id;
            }
            return self::getRedis()->delete($cacheIds);
        }

        $ret = true;
        foreach ($cacheIds as $cacheId) {
            if (self::getBackend() == 'memcached') {
                $r = self::getMemcached()->delete(self::_getMemcachedPrefix().md5($cacheId));
            } else if (self::getBackend() == 'memcache') {
                $r = self::getMemcache()->delete(self::_getMemcachePrefix().md5($cacheId));
            } else if (self::getBackend() == 'apc') {
                static $prefix;
                if (!isset($prefix)) $prefix = self::$uniquePrefix.'-';
                $r = apc_delete($prefix.$cacheId);
            } else if (self::getBackend() == 'apcu') {
                static $prefix;
                if (!isset($prefix)) $prefix = self::$uniquePrefix.'-';
                $r = apcu_delete($prefix.$cacheId);
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
        if ((self::getBackend() == 'apc' || self::getBackend() == 'apcu') && PHP_SAPI == 'cli') {
            $ret = Kwf_Util_Apc::callClearCacheByCli(array('deleteCacheSimple' => implode(',', $cacheIds)));
        }
        return $ret;
    }

    public static function clear($cacheIdPrefix)
    {
        throw new Kwf_Exception("don't delete the whole cache");
    }

    //only call from Kwf_Util_ClearCache_Types_SimpleCache!
    public static function _clear()
    {
        if (self::getBackend() == 'memcached') {
            if (!Kwf_Config::getValue('cacheSimpleNamespace')) {
                //increment namespace
                $mc = Kwf_Cache_Simple::getMemcached();
                if ($mc->get(Kwf_Cache_Simple::$uniquePrefix.'cache_namespace')) {
                    $mc->increment(Kwf_Cache_Simple::$uniquePrefix.'cache_namespace');
                }
            } else {
                $mc = Kwf_Cache_Simple::getMemcached();
                $mc->flush();
            }
        } else if (self::getBackend() == 'memcache') {
            if (!Kwf_Config::getValue('cacheSimpleNamespace')) {
                //increment namespace
                $mc = Kwf_Cache_Simple::getMemcache();
                if ($mc->get(Kwf_Cache_Simple::$uniquePrefix.'cache_namespace')) {
                    $mc->increment(Kwf_Cache_Simple::$uniquePrefix.'cache_namespace');
                }
            } else {
                $mc = Kwf_Cache_Simple::getMemcache();
                $mc->flush();
            }
        } else if (self::getBackend() == 'redis') {
            $prefixLength = strlen(self::getRedis()->_prefix(''));
            $it = null;
            while ($keys = self::getRedis()->scan($it, self::getRedis()->_prefix('simple:*'))) {
                foreach ($keys as $k=>$i) {
                    $keys[$k] = substr($i, $prefixLength);
                }
                self::getRedis()->delete($keys);
            }
        } else if (self::getBackend() == 'file') {
            foreach (glob('cache/simple/*') as $i) {
                unlink($i);
            }
        } else if (self::getBackend() == 'apc' || self::getBackend() == 'apcu') {
            //those are cleared using their own clear-cache type
        } else {
            if (!isset(self::$_zendCache)) self::getZendCache();
            $r = self::$_zendCache->clean(Zend_Cache::CLEANING_MODE_ALL);
        }
    }

    public static function getUniquePrefix()
    {
        return self::$uniquePrefix;
    }
}
