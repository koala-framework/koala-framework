<?php
class Vpc_Basic_Text_StylesAsset implements Vps_Assets_Dynamic_Interface
{
    public function __construct(Vps_Assets_Loader $loader, $assetsType, $rootComponent)
    {
    }

    public function getContents()
    {
        return Vpc_Basic_Text_StylesModel::getStylesContents();
    }

    public function getMTimeFiles()
    {
        return array();
    }

    public function getMTime()
    {
        return Vpc_Basic_Text_StylesModel::getMTime();
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