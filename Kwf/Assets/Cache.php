<?php
class Kwf_Assets_Cache
{
    public static function _getSlowCache()
    {
        static $ret;
        if (!isset($ret)) {
            $ret = new Zend_Cache_Core(array(
                'lifetime' => null,
                'automatic_serialization' => true,
                'automatic_cleaning_factor' => 0,
                'write_control' => false,
            ));
            $ret->setBackend(new Zend_Cache_Backend_File(array(
                'cache_dir' => 'cache/assets',
                'cache_file_umask' => 0666,
                'hashed_directory_umask' => 0777,
                'hashed_directory_level' => 2,
            )));
        }
        return $ret;
    }

    public static function getInstance()
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new self();
        }
        return $cache;
    }

    public function load($cacheId)
    {
        $ret = Kwf_Cache_SimpleStatic::fetch('as-'.$cacheId);
        if ($ret === false) {
            $ret = self::_getSlowCache()->load($cacheId);
            if ($ret !== false) {
                Kwf_Cache_SimpleStatic::add('as-'.$cacheId, $ret);
            }
        }
        return $ret;
    }


    public function save($cacheData, $cacheId)
    {
        $ret = Kwf_Cache_SimpleStatic::add('as-'.$cacheId, $cacheData);
        return $ret && self::_getSlowCache()->save($cacheData, $cacheId);
    }

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

    public function clean()
    {
        Kwf_Cache_SimpleStatic::clear('as-');
        return self::_getSlowCache()->clean();
    }

    public function remove($cacheId)
    {
        Kwf_Cache_SimpleStatic::_delete('as-'.$cacheId);
        return self::_getSlowCache()->remove($cacheId);
    }
}
