<?php
class Vps_Assets_DynamicNotInAllWithArgument_Asset implements Vps_Assets_Dynamic_Interface
{
    public static $file;

    private $_arguments;
    public function __construct(Vps_Assets_Loader $loader, $assetsType, $rootComponent, $arguments)
    {
        $this->_arguments = $arguments;
    }

    public function getContents()
    {
        return str_replace('{arg}', $this->_arguments[0], file_get_contents(self::$file));
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