<?php
class Vps_View_Helper_Assets
{
    private $_dep;
    public function __construct($dep = null)
    {
        if (!$dep) $dep = new Vps_Assets_Dependencies();
        $this->_dep = $dep;
    }
    public function assets($type, $section = 'web')
    {
        $indent = str_repeat(' ', 8);
        $ret = '';
        $rootComponent = Vps_Component_Data_Root::getComponentClass();
        foreach ($this->_dep->getAssetUrls($type, 'css', $section, $rootComponent) as $file) {
            $ret .= "$indent<link rel=\"stylesheet\" type=\"text/css\" href=\"$file\" />\n";
        }
        foreach ($this->_dep->getAssetUrls($type, 'printcss', $section, $rootComponent) as $file) {
            $ret .= "$indent<link rel=\"stylesheet\" type=\"text/css\" href=\"$file\" ";
            if (!Zend_Registry::get('config')->debug->assets->usePrintCssForAllMedia) {
                $ret .= "media=\"print\" ";
            }
            $ret .= "/>\n";
        }
        foreach ($this->_dep->getAssetUrls($type, 'js', $section, $rootComponent) as $file) {
            $ret .= "$indent<script type=\"text/javascript\" src=\"$file\"></script>\n";
        }
        return $ret;
    }
}
