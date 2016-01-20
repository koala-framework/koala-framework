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
        $contents = file_get_contents($f);
        $contents = rtrim($contents);
        $contents = explode("\n", $contents);
        if (substr($contents[count($contents)-1], 0, 21) == '//# sourceMappingURL=') {
            //remove sourceMappingURL comment
            unset($contents[count($contents)-1]);
        }
        $contents = implode("\n", $contents);

        $paths = self::_getAllPaths();
        $pathType = $this->getType();
        $f = $paths[$pathType].substr($this->_sourceMapFile, strpos($this->_sourceMapFile, '/'));
        $mapContents = file_get_contents($f);

        $cacheFile = sys_get_temp_dir().'/kwf-uglifyjs/'.$this->getFileNameWithType().'.map.'.md5($mapContents);
        if (!file_exists($cacheFile)) {
            $map = new Kwf_SourceMaps_SourceMap($mapContents, $contents);
            if (!is_dir(dirname($cacheFile))) mkdir(dirname($cacheFile), 0777, true);
            $data = $map->getMapContentsData();
            if (count($data->sources) != 1) {
                throw new Kwf_Exception('map must consist only of a single source');
            }
            $data->sources = array(
                $this->getFileNameWithType()
            );
            $map->save($cacheFile);
        } else {
            $map = new Kwf_SourceMaps_SourceMap(file_get_contents($cacheFile), $contents);
        }
        return $map;
    }
}
