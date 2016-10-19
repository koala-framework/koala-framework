<?php
class Kwf_Assets_BuildCache_File
{
    static private $_buildDir = "build/assets";
    public $building = false;
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
        $fileName = self::$_buildDir.'/'.$cacheId;
        if (!file_exists($fileName)) {
            return false;
        }
        return unserialize(file_get_contents($fileName));
    }


    public function save($cacheData, $cacheId)
    {
        if (!file_exists(self::$_buildDir)) {
            mkdir(self::$_buildDir, 0777, true);
        }
        $fileName = self::$_buildDir.'/'.$cacheId;
        return file_put_contents($fileName, serialize($cacheData));
    }

    public function clean()
    {
        if (!$this->building) {
            throw new Kwf_Exception("Can't clear out of build");
        }
        foreach (glob(self::$_buildDir.'/*') as $f) {
            unlink($f);
        }
    }

    public function remove($cacheIds)
    {
        if (!is_array($cacheIds)) $cacheIds = array($cacheIds);
        foreach ($cacheIds as $cacheId) {
            $fileName = self::$_buildDir.'/'.$cacheId;
            if (file_exists($fileName)) {
                unlink($fileName);
                return true;
            }
        }
    }

}
