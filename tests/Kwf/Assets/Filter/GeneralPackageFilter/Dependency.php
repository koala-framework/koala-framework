<?php
class Kwf_Assets_Filter_GeneralPackageFilter_Dependency extends Kwf_Assets_Dependency_Abstract
{
    public static $contents;
    public static $mtime;
    public function getMimeType()
    {
        return 'text/css';
    }

    public function getContents($language)
    {
        return self::$contents;
    }

    public function getMTime()
    {
        return self::$mtime;
    }
}
