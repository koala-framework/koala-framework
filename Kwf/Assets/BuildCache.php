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

    public function load($cacheId)
    {
        $ret = Kwf_Cache_SimpleStatic::fetch('asb-'.$cacheId);
        if ($ret === false) {
            $fileName = self::$_buildDir.'/'.$cacheId;
            if (!file_exists($fileName)) {
                return false;
            }
            $ret = unserialize(file_get_contents($fileName));
            if ($ret !== false) {
                Kwf_Cache_SimpleStatic::add('asb-'.$cacheId, $ret);
            }
        }
        return $ret;
    }


    public function save($cacheData, $cacheId)
    {
        if (!file_exists(self::$_buildDir)) {
            mkdir(self::$_buildDir, 0777, true);
        }
        Kwf_Cache_SimpleStatic::add('asb-'.$cacheId, $cacheData);
        $fileName = self::$_buildDir.'/'.$cacheId;
        return file_put_contents($fileName, serialize($cacheData));
    }
/*
    public function test($cacheId)
    {
        $ret = Kwf_Cache_SimpleStatic::fetch('as-mtime-'.$cacheId);
        if ($ret === false) {
            $ret = self::_getSlowCache()->test($cacheId);
            if ($ret !== false) {
                Kwf_Cache_SimpleStatic::add('as-mtime-'.$cacheId, $ret);
            }
        }
        return $ret;
    }
*/
    public function clean()
    {
        if (!$this->building) {
            throw new Kwf_Exception("Can't clear out of build");
        }
        Kwf_Cache_SimpleStatic::clear('asb-');
        foreach (glob(self::$_buildDir.'/*') as $f) {
            unlink($f);
        }
    }

    public function remove($cacheIds)
    {
        if (!is_array($cacheIds)) $cacheIds = array($cacheIds);
        $staticIds = array();
        foreach ($cacheIds as $cacheId) {
            $fileName = self::$_buildDir.'/'.$cacheId;
            if (file_exists($fileName)) {
                unlink($fileName);
                return true;
            }
            $staticIds[] = 'asb-'.$cacheId;
        }
        Kwf_Cache_SimpleStatic::_delete($staticIds);
    }
}
