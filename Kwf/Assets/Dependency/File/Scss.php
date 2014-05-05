<?php
class Kwf_Assets_Dependency_File_Scss extends Kwf_Assets_Dependency_File_Css
{
    public function getContents($language)
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new Zend_Cache_Core(array(
                'lifetime' => null,
                'automatic_serialization' => true,
                'automatic_cleaning_factor' => 0,
                'write_control' => false,
            ));
            $cache->setBackend(new Zend_Cache_Backend_File(array(
                'cache_dir' => 'cache/scss',
                'cache_file_umask' => 0666,
            )));
        }

        $fileName = $this->getFileName();
        $cacheId = 'v2'.str_replace(array('\\', ':', '/', '.', '-'), '_', $fileName);

        $ret = $cache->load($cacheId);
        if ($ret && !isset($ret['mtime'])) {
            $ret = false;
        }
        if ($ret && $ret['mtime'] != filemtime($fileName)) {
            //file modified, invalidate
            $ret = false;
        }
        if ($ret) {
            $ret = $ret['contents'];
        }

        if ($ret === false) {

            $sassc = Kwf_Config::getValue('server.sassc');
            $loadPath = array(
                Kwf_Config::getValue('externLibraryPath.compassMixins').'/lib',
                Kwf_Config::getValue('externLibraryPath.susy').'/sass',
                './scss',
                KWF_PATH.'/sass/Kwf/stylesheets',
            );
            $loadPath = escapeshellarg(implode(PATH_SEPARATOR, $loadPath));
            $fileName = escapeshellarg($this->getFileName());
            $outFile = tempnam('temp', 'outcss');
            $cmd = "$sassc --load-path $loadPath --style nested $fileName $outFile";
            exec($cmd, $out, $retVal);
            if ($retVal) {
                throw new Kwf_Exception("sassc failed: ".implode("\n", $out));
            }
            $ret = file_get_contents($outFile);
            unlink($outFile);

            $cache->save(
                array('mtime'=>filemtime($this->getFileName()), 'contents'=>$ret),
                $cacheId
            );
        }

        $ret = $this->_processContents($ret);
        return $ret;
    }
}
