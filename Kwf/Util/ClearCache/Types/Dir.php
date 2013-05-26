<?php
class Kwf_Util_ClearCache_Types_Dir extends Kwf_Util_ClearCache_Types_Abstract
{
    private $_dir;
    public function __construct($dir)
    {
        $this->_dir = $dir;
    }

    protected function _clearCache($options)
    {
        if (is_dir("cache/$this->_dir")) {
            $this->_removeDirContents("cache/$this->_dir");
        } else if (is_dir($this->_dir)) {
            $this->_removeDirContents($this->_dir);
        }
    }

    private function _removeDirContents($path)
    {
        $dir = new DirectoryIterator($path);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isFile() && $fileinfo->getFilename() != '.gitignore' && substr($fileinfo->getFilename(), 0, 4) != '.nfs') {
                unlink($fileinfo->getPathName());
            } elseif (!$fileinfo->isDot() && $fileinfo->isDir() && $fileinfo->getFilename() != '.svn') {
                $this->_removeDirContents($fileinfo->getPathName());
                @rmdir($fileinfo->getPathName());
            }
        }
    }

    public function getTypeName()
    {
        return $this->_dir;
    }
    public function doesClear() { return true; }
    public function doesRefresh() { return false; }
}
