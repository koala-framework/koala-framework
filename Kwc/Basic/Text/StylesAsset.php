<?php
class Kwc_Basic_Text_StylesAsset implements Kwf_Assets_Dynamic_Interface
{
    private $_stylesModel;
    public function __construct(Kwf_Assets_Loader $loader, $assetsType, $rootComponent, $arguments)
    {
        if (!isset($arguments[0])) throw new Kwf_Exception_NotFound();
        $this->_stylesModel = $arguments[0];
    }

    public function getContents()
    {
        return Kwf_Model_Abstract::getInstance($this->_stylesModel)->getStylesContents();
    }

    public function getMTimeFiles()
    {
        return array();
    }

    public function getMTime()
    {
        return Kwf_Model_Abstract::getInstance($this->_stylesModel)->getMTime();
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
