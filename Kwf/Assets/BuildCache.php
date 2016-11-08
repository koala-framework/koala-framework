<?php
class Kwf_Assets_BuildCache
{
    static private $_buildDir = "build/assets";
    public $building = false;
    public static function getInstance()
    {
        static $cache;
        if (!isset($cache)) {
            if (Kwf_Config::getValue('assets.useCacheSimpleStatic')) {
                //two level cache, SimpleStatic (apc) plus file
                $cache = new self();
            } else {
                //only file
                $cache = new Kwf_Assets_BuildCache_File();
            }
        }
        return $cache;
    }

    private static function _getFileCacheInstance()
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new Kwf_Assets_BuildCache_File();
        }
        return $cache;
    }

    public function load($cacheId)
    {
        $ret = Kwf_Cache_SimpleStatic::fetch('asb-'.$cacheId);
        if ($ret === false) {
            $ret = self::_getFileCacheInstance()->load($cacheId);
            if ($ret !== false) {
                Kwf_Cache_SimpleStatic::add('asb-'.$cacheId, $ret);
            }
        }
        return $ret;
    }

    public function save($cacheData, $cacheId)
    {
        Kwf_Cache_SimpleStatic::add('asb-'.$cacheId, $cacheData);
        return self::_getFileCacheInstance()->save($cacheData, $cacheId);
    }

    public function clean()
    {
        if (!$this->building) {
            throw new Kwf_Exception("Can't clear out of build");
        }
        Kwf_Cache_SimpleStatic::clear('asb-');
        self::_getFileCacheInstance()->clean();
    }

    public function remove($cacheIds)
    {
        if (!is_array($cacheIds)) $cacheIds = array($cacheIds);
        self::_getFileCacheInstance()->remove($cacheIds);
        $staticIds = array();
        foreach ($cacheIds as $cacheId) {
            $staticIds[] = 'asb-'.$cacheId;
        }
        Kwf_Cache_SimpleStatic::_delete($staticIds);
    }
}
