<?php
/**
 * Cache for media output
 *
 * Will NOT get deleted on clear-cache
 */
class Kwf_Media_MemoryCache
{
    private static $_zendCache = null;
    /**
     * @var self
     */
    private static $_instance;


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

    //for 'file' backend
    private static function _getFileNameForCacheId($cacheId)
    {
        $cacheId = preg_replace('#[^a-zA-Z0-9_-]#', '_', $cacheId);
        return 'cache/media/mem-'.$cacheId;
    }

    public function load($id)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            return Kwf_Cache_Simple::getMemcache()->get($prefix.$id);
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
            return Kwf_Cache_Simple::fetch('media-'.$id);
        }
    }

    public function save($data, $id, $ttl = null)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            $flags = is_int($data) ? null : MEMCACHE_COMPRESSED;
            return Kwf_Cache_Simple::getMemcache()->set($prefix.$id, $data, $flags, $ttl);
        } else if ($be == 'file') {
            $file = self::_getFileNameForCacheId($id);
            $data = array(
                'contents' => $data,
                'expire' => $ttl ? time()+$ttl : null
            );
            return file_put_contents($file, serialize($data));
        } else {
            return Kwf_Cache_Simple::add('media-'.$id, $data, $ttl);
        }

    }

    public function remove($id)
    {
        $be = Kwf_Cache_Simple::getBackend();
        if ($be == 'memcache') {
            static $prefix;
            if (!isset($prefix)) $prefix = Kwf_Cache_Simple::getUniquePrefix().'-media-';
            return Kwf_Cache_Simple::getMemcache()->delete($prefix.$id);
        } else if ($be == 'file') {
            $file = self::_getFileNameForCacheId($id);
            if (!file_exists($file)) return false;
            unlink($file);
            return true;
        } else {
            return Kwf_Cache_Simple::delete('media-'.$id);
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
        if ($be == 'memcache') {
            return Kwf_Cache_Simple::getMemcache()->flush();
        } else if ($be == 'file') {
            foreach (glob('cache/media/mem-*') as $i) {
                unlink($i);
            }
            return true;
        } else {
            return Kwf_Cache_Simple::clear('media-');
        }
    }

}
