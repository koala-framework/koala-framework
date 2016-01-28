<?php
class Kwf_Assets_UseRequire3_TestDependency extends Kwf_Assets_Dependency_Abstract
{
    private $_contents;
    public function __construct(Kwf_Assets_ProviderList_Abstract $providerList, $contents)
    {
        parent::__construct($providerList);
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
        return $this->_contents."\n";
    }
}
