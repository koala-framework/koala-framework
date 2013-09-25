<?php
class Kwf_Assets_Dependency_File_Scss extends Kwf_Assets_Dependency_File_Css
{
    public function getContents($language)
    {
        static $cache;
        if (!isset($cache)) {
            $cache = new Zend_Cache_Core(array(
                'lifetime' => null,
                'automatic_serialization' => false,
                'automatic_cleaning_factor' => 0,
                'write_control' => false,
            ));
            $cache->setBackend(new Zend_Cache_Backend_File(array(
                'cache_dir' => 'cache/scss',
                'cache_file_umask' => 0666,
            )));
        }

        $fileName = $this->getFileName();
        $cacheId = str_replace(array('/', '.'), '_', $fileName);
        $mtime = $cache->test($cacheId);
        $ret = false;
        if ($mtime && filemtime($fileName) < $mtime) {
            $ret = $cache->load($cacheId);
        }
        if (!$ret) {
            $ret = Kwf_Assets_Dependency_File::getContents($language);

            static $scssParser;
            if (!isset($scssParser)) {
                $scssParser = new Kwf_Util_SassParser(array(
                    'style' => 'compact',
                    'cache' => false,
                    'syntax' => 'scss',
                    'debug' => true,
                    'debug_info' => false,
                    'load_path_functions' => array('Kwf_Util_SassParser::loadCallback'),
                    'functions' => Kwf_Util_SassParser::getExtensionsFunctions(array('Compass', 'Susy', 'Kwf')),
                    'extensions' => array('Compass', 'Susy', 'Kwf')
                ));
            }
            $ret = $scssParser->toCss($ret, false);
            $cache->save($ret, $cacheId);
        }

        $ret = $this->_processContents($ret);
        return $ret;
    }
}
