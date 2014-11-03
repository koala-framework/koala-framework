<?php
class Kwf_Assets_Dependency_File_JsPreBuilt extends Kwf_Assets_Dependency_File
{
    protected $_builtFile;
    protected $_sourceMapFile;

    public function __construct($fileName, $builtFile, $sourceMapFile)
    {
        parent::__construct($fileName);
        $this->_builtFile = $builtFile;
        $this->_sourceMapFile = $sourceMapFile;
    }

    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContentsPacked($language)
    {
        $paths = self::_getAllPaths();
        $pathType = $this->getType();
        $f = $paths[$pathType].substr($this->_builtFile, strpos($this->_builtFile, '/'));
        return file_get_contents($f);
    }

    public function getContentsPackedSourceMap($language)
    {
        $paths = self::_getAllPaths();
        $pathType = $this->getType();
        $f = $paths[$pathType].substr($this->_sourceMapFile, strpos($this->_sourceMapFile, '/'));
        $mapContents = file_get_contents($f);
        $map = new Kwf_Assets_Util_SourceMap($mapContents, $this->getContentsPacked($language));

        $cacheFile = sys_get_temp_dir().'/kwf-uglifyjs/'.$this->getFileNameWithType().'.map.'.md5($mapContents);
        if (!file_exists($cacheFile)) {
            if (!is_dir(dirname($cacheFile))) mkdir(dirname($cacheFile), 0777, true);
            file_put_contents($cacheFile, $map->getMapContents());
        }
        return file_get_contents($cacheFile);
    }
}
