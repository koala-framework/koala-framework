<?php
class Kwc_Basic_Text_StylesAsset extends Kwf_Assets_Dependency_File
    implements Kwf_Assets_Interface_UrlResolvable
{
    private $_stylesModel;
    public function __construct($stylesModel)
    {
        $this->_stylesModel = $stylesModel;
    }

    public function toUrlParameter()
    {
        return $this->_stylesModel;
    }

    public static function fromUrlParameter($class, $parameter)
    {
        $param = explode(':', $parameter);
        return new $class($param[0]);
    }

    public function getContents()
    {
        return Kwf_Model_Abstract::getInstance($this->_stylesModel)->getStylesContents();
    }

    public function getMTime()
    {
        return Kwf_Model_Abstract::getInstance($this->_stylesModel)->getMTime();
    }

    public function getMimeType()
    {
        return 'text/css';
    }

    public function getIncludeInPackage()
    {
        return false;
    }
}
