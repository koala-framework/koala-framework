<?php
class Kwf_Assets_UseRequire2_TestDependency extends Kwf_Assets_Dependency_Abstract
{
    private $_contents;
    public function __construct($contents)
    {
        $this->_contents = $contents;
    }

    public function getContentsPacked($language)
    {
        $ret = $this->_contents;
        return Kwf_SourceMaps_SourceMap::createEmptyMap($ret);
    }


    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function toDebug()
    {
        return get_class($this).': '.$this->_contents."\n";
    }
}
