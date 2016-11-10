<?php
class Kwf_Assets_Cache
{
    public static function getInstance()
    {
        static $cache;
        if (!isset($cache)) {
            if (Kwf_Config::getValue('assets.useCacheSimpleStatic')) {
                //two level cache, SimpleStatic (apc) plus file
                $cache = new self();
            } else {
                //only file
                $cache = new Kwf_Assets_Cache_File();
            }
        }
        return $cache;
    }

    private static function _getFileCacheInstance()
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new Kwf_Assets_Cache_File();
        }
        return $cache;
    }

    public function load($cacheId)
    {
        $ret = Kwf_Cache_SimpleStatic::fetch('as-'.$cacheId);
        if ($ret === false) {
            $ret = self::_getFileCacheInstance()->load($cacheId);
            if ($ret !== false) {
                Kwf_Cache_SimpleStatic::add('as-'.$cacheId, $ret);
            }
        }
        return $ret;
    }


    public function save($cacheData, $cacheId)
    {
        $ret = Kwf_Cache_SimpleStatic::add('as-'.$cacheId, $cacheData);
        return $ret && self::_getFileCacheInstance()->save($cacheData, $cacheId);
    }

    public function test($cacheId)
    {
        $ret = Kwf_Cache_SimpleStatic::fetch('as-mtime-'.$cacheId);
        if ($ret === false) {
            $ret = self::_getFileCacheInstance()->test($cacheId);
            if ($ret !== false) {
                Kwf_Cache_SimpleStatic::add('as-mtime-'.$cacheId, $ret);
            }
        }
        return $ret;
    }

    public function clean()
    {
        Kwf_Cache_SimpleStatic::clear('as-');
        return self::_getFileCacheInstance()->clean();
    }

    public function remove($cacheIds)
    {
        if (!is_array($cacheIds)) $cacheIds = array($cacheIds);
        $staticIds = array();
        foreach ($cacheIds as $cacheId) {
            self::_getFileCacheInstance()->remove(str_replace('-', '_', $cacheId));
            $staticIds[] = 'as-'.$cacheId;
        }
        Kwf_Cache_SimpleStatic::_delete($staticIds);
    }
}
