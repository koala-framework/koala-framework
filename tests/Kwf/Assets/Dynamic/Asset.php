<?php
class Kwf_Assets_Dynamic_Asset implements Kwf_Assets_Dynamic_Interface
{
    public static $file;
    public function __construct(Kwf_Assets_Loader $loader, $assetsType, $rootComponent, $arguments)
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
        return true;
    }
}