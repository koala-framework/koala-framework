<?php
class Kwf_Update_20150309Legacy38002 extends Kwf_Update
{
    public function getTags()
    {
        return array('fulltext');
    }

    public function update()
    {
        echo "\n\nremoving fulltext index, you need to rebuild manually\n";
        if (file_exists('cache/fulltext')) self::_removeDirContents('cache/fulltext');
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
}
