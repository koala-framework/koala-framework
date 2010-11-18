<?php
class Vps_Assets_DynamicNotInAll_Asset implements Vps_Assets_Dynamic_Interface
{
    public static $file;
    public function __construct(Vps_Assets_Loader $loader, $assetsType, $rootComponent)
    {
    }

    public function getContents()
    {
        return file_get_contents(self::$file);
    }

    public function getMTimeFiles()
    {
        return array(self::$file);
    }

    public function getMTime()
    {
        return null;
    }

    public function getType()
    {
        return 'css';
    }

    public function getIncludeInAll()
    {
        return false;
    }
}