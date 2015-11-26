<?php
class Kwf_Assets_Filter_GeneralPackageFilter_Dependency extends Kwf_Assets_Dependency_Abstract
{
    public static $contents;
    public function getMimeType()
    {
        return 'text/css';
    }

    public function getContentsPacked($language)
    {
        $ret = self::$contents;
        return Kwf_SourceMaps_SourceMap::createEmptyMap($ret);
    }
}
