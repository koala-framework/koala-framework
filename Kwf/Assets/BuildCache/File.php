<?php
class Kwf_Assets_BuildCache_File
{
    static private $_buildDir = "build/assets";

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
        if (!Kwf_Assets_BuildCache::getInstance()->building) {
            throw new Kwf_Exception("Can't clear out of build");
        }
        $this->_deleteFiles(self::$_buildDir);
    }

    public function remove($cacheIds)
    {
        if (!is_array($cacheIds)) $cacheIds = array($cacheIds);
        foreach ($cacheIds as $cacheId) {
            $fileName = self::$_buildDir.'/'.$cacheId;
            if (file_exists($fileName)) {
                unlink($fileName);
            }
        }
    }

    protected function _deleteFiles($target)
    {
        if (is_dir($target)){
            $files = glob($target . '*', GLOB_MARK); //GLOB_MARK adds a slash to directories returned
            foreach ($files as $file) {
                $this->_deleteFiles($file);
            }
        } elseif (is_file($target)) {
            unlink($target);
        }
    }
}
