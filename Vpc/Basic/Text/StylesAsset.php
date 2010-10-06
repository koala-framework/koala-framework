<?php
class Vpc_Basic_Text_StylesAsset implements Vps_Assets_Dynamic_Interface
{
    private $_stylesModel;
    public function __construct(Vps_Assets_Loader $loader, $assetsType, $rootComponent, $arguments)
    {
        $this->_stylesModel = $arguments[0];
    }

    public function getContents()
    {
        return Vps_Model_Abstract::getInstance($this->_stylesModel)->getStylesContents();
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