<?php
class Kwf_Media_OutputCacheFileBackend extends Kwf_Cache_Backend_File
{
    protected function _fileGetContents($file)
    {
        $ret = parent::_fileGetContents($file);
        Kwf_Util_Media::onCacheFileAccess($file);
        return $ret;
    }

    protected function _filePutContents($file, $string)
    {
        $ret = parent::_filePutContents($file, $string);
        Kwf_Util_Media::onCacheFileAccess($file);
        return $ret;
    }
}
