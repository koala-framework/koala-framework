<?php
class Vps_View_Helper_Assets
{
    private $_dep;
    public function __construct($dep = null)
    {
        if (!$dep) {
            $l = new Vps_Assets_Loader();
            $dep = $l->getDependencies();
        }
        $this->_dep = $dep;
    }
    public function assets($type, $section = 'web', $language = null)
    {
        $indent = str_repeat(' ', 8);
        $ret = '';
        $rootComponent = Vps_Component_Data_Root::getComponentClass();
        foreach ($this->_dep->getAssetUrls($type, 'css', $section, $rootComponent, $language) as $file) {
            $ret .= "$indent<link rel=\"stylesheet\" type=\"text/css\" href=\"".htmlspecialchars($file)."\" />\n";
        }
        foreach ($this->_dep->getAssetUrls($type, 'printcss', $section, $rootComponent, $language) as $file) {
            $ret .= "$indent<link rel=\"stylesheet\" type=\"text/css\" href=\"".htmlspecialchars($file)."\" ";
            if (!Vps_Config::getValue('debug.assets.usePrintCssForAllMedia')) {
                $ret .= "media=\"print\" ";
            }
            $ret .= "/>\n";
        }
        foreach ($this->_dep->getAssetUrls($type, 'js', $section, $rootComponent, $language) as $file) {
            $ret .= "$indent<script type=\"text/javascript\" src=\"".htmlspecialchars($file)."\"></script>\n";
        }
        return $ret;
    }
}
