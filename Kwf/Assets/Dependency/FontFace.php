<?php
class Kwf_Assets_Dependency_FontFace extends Kwf_Assets_Dependency_Abstract
{
    public function __construct(Kwf_Assets_ProviderList_Abstract $providerList, $name, $path)
    {
        $this->_path = $path;
        $this->_name = $name;
        parent::__construct($providerList);
    }

    public function getMimeType()
    {
        return 'text/css';
    }

    public function getContentsPacked()
    {
        $basePath = 'vendor/bower_components/';
        if (file_exists($basePath.$this->_path."/fonts/$this->_name.eot")) {
            //eg icomoon, ionicons
            $fontsPath = "/assets/$this->_path/fonts/$this->_name";
        } else if (file_exists($basePath.$this->_path."/fonts/".strtolower($this->_name)."-webfont.eot")) {
            //eg Font-Awesome
            $fontsPath = "/assets/$this->_path/fonts/".strtolower($this->_name)."-webfont";
        } else {
            throw new Kwf_Exception("Can't detect path of font");
        }
        $ret  = "";
        $ret .= "@font-face {\n";
        $ret .= "    font-family: '".$this->_name."';\n";
        $ret .= "    src: url('".$fontsPath.".eot');\n";
        $ret .= "    src: url('".$fontsPath.".eot?#iefix') format('eot'),\n";
        $ret .= "         url('".$fontsPath.".woff') format('woff'),\n";
        $ret .= "         url('".$fontsPath.".ttf') format('truetype'),\n";
        $ret .= "         url('".$fontsPath.".svg') format('svg');\n";
        $ret .= "}\n";
        return Kwf_SourceMaps_SourceMap::createEmptyMap($ret);
    }

    public function __toString()
    {
        return 'FontFace'.ucfirst($this->_name);
    }
}
