<?php
class Kwf_Assets_Filter_GeneralPackageFilter_Filter extends Kwf_Assets_Filter_Abstract
{
    public function filter(Kwf_SourceMaps_SourceMap $sourcemap)
    {
        $sourcemap->stringReplace('$red', '#ff0000');
        $sourcemap->stringReplace('$blue', '#0000ff');
        return $sourcemap;
    }

    public function getExecuteFor()
    {
        return self::EXECUTE_FOR_PACKAGE;
    }

    public function getMimeType()
    {
        return 'text/css';
    }
}
