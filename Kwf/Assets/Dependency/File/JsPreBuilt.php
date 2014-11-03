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
        return file_get_contents($f);
    }
}
