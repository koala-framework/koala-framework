<?php
/**
 * A simple and fast cache. Doesn't have all the Zend_Cache bloat.
 *
 * If available it uses apc user cache direclty (highly recommended!!), else it falls
 * back to Zend_Cache using a memcache backend.
 *
 * If aws.simpleCacheCluster is set Aws ElastiCache will be used.
 */
class Kwf_Cache_Simple
{
    private static $_zendCache = null;

    public static function resetZendCache()
    {
        self::$_zendCache = null;
        Kwf_Cache_SimpleStatic::resetZendCache();
    }

    public static function getZendCache()
    {
        if (!isset(self::$_zendCache)) {
            if (Kwf_Config::getValue('aws.simpleCacheCluster')) {
                self::$_zendCache = new Zend_Cache_Core(array(
                    'lifetime' => null,
                    'write_control' => false,
                    'automatic_cleaning_factor' => 0,
                    'automatic_serialization' => true
                ));
                self::$_zendCache->setBackend(new Kwf_Util_Aws_ElastiCache_CacheBackend(array(
                    'cacheClusterId' => Kwf_Config::getValue('aws.simpleCacheCluster'),
                )));
            } else {
                if (!Kwf_Config::getValue('server.memcache.host') && extension_loaded('apc')) {
                    self::$_zendCache = false;
                } else {
                    self::$_zendCache = new Zend_Cache_Core(array(
                        'lifetime' => null,
                        'write_control' => false,
                        'automatic_cleaning_factor' => 0,
                        'automatic_serialization' => true
                    ));
                    if (Kwf_Config::getValue('server.memcache.host')) {
                        self::$_zendCache->setBackend(new Kwf_Cache_Backend_Memcached());
                    } else {
                        //fallback to file backend (NOT recommended!)
                        self::$_zendCache->setBackend(new Kwf_Cache_Backend_File(array(
                            'cache_dir' => 'cache/simple'
                        )));
                    }
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

    public static function fetch($cacheId, &$success = true)
    {
        if (!isset(self::$_zendCache)) self::getZendCache();
        if (!self::$_zendCache) {
            static $prefix;
            if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
            return apc_fetch($prefix.$cacheId, $success);
        } else {
            $ret = self::$_zendCache->load(self::_processId($cacheId));
            $success = $ret !== false;
            return $ret;
        }
    }

    public static function add($cacheId, $data, $ttl = null)
    {
        if (!isset(self::$_zendCache)) self::getZendCache();
        if (!self::$_zendCache) {
            static $prefix;
            if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
            return apc_add($prefix.$cacheId, $data, $ttl);
        } else {
            return self::$_zendCache->save($data, self::_processId($cacheId), array(), $ttl);
        }
    }

    public static function delete($cacheIds)
    {
        if (!isset(self::$_zendCache)) self::getZendCache();

        if (!is_array($cacheIds)) $cacheIds = array($cacheIds);
        static $prefix;
        if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
        $ret = true;
        $ids = array();
        foreach ($cacheIds as $cacheId) {
            if (!self::$_zendCache) {
                $r = apc_delete($prefix.$cacheId);
                $ids[] = $prefix.$cacheId;
            } else {
                $r = self::$_zendCache->remove(self::_processId($cacheId));
            }
            if (!$r) $ret = false;
        }
        if (!self::$_zendCache && php_sapi_name() == 'cli' && $ids) {
            $result = Kwf_Util_Apc::callClearCacheByCli(array('cacheIds' => implode(',', $ids)), Kwf_Util_Apc::SILENT);
            if (!$result['result']) $ret = false;
        }
        return $ret;
    }

    public static function clear($cacheIdPrefix)
    {
        if (!isset(self::$_zendCache)) self::getZendCache();

        if (!self::$_zendCache) {
            if (!class_exists('APCIterator')) {
                apc_clear_cache('user');
            } else {
                static $prefix;
                if (!isset($prefix)) $prefix = self::getUniquePrefix().'-';
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
            //we can't do any better here :/
            if (Kwf_Config::getValue('aws.simpleCacheCluster')) {
                throw new Kwf_Exception_NotYetImplemented("We don't want to clear the whole");
            }
            self::$_zendCache->clean();
        }
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
