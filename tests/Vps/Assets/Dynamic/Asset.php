<?php
class Vps_Assets_Dynamic_Asset implements Vps_Assets_Dynamic_Interface
{
    public static $file;
    public function __construct(Vps_Assets_Loader $loader, $assetsType, $rootComponent)
    {
    }

    public function getContents()
    {
        return file_Get_contents(self::$file);
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
}