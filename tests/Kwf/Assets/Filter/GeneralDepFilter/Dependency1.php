<?php
class Kwf_Assets_Filter_GeneralDepFilter_Dependency1 extends Kwf_Assets_Dependency_Abstract
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
