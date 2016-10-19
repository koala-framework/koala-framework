<?php
class Kwf_Assets_Cache_File
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
                'cache_file_perm' => 0666,
                'hashed_directory_perm' => 0777,
                'hashed_directory_level' => 2,
            )));
        }
        return $ret;
    }

    public function load($cacheId)
    {
        return self::_getSlowCache()->load(str_replace('-', '_', $cacheId));
    }


    public function save($cacheData, $cacheId)
    {
        return self::_getSlowCache()->save($cacheData, str_replace('-', '_', $cacheId));
    }

    public function test($cacheId)
    {
        return self::_getSlowCache()->test(str_replace('-', '_', $cacheId));
    }

    public function clean()
    {
        return self::_getSlowCache()->clean();
    }

    public function remove($cacheIds)
    {
        if (!is_array($cacheIds)) $cacheIds = array($cacheIds);
        foreach ($cacheIds as $cacheId) {
            self::_getSlowCache()->remove(str_replace('-', '_', $cacheId));
        }
    }
}
