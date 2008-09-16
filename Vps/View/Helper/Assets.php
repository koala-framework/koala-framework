<?php
class Vps_View_Helper_Assets
{
    public function assets($type)
    {
        $dep = new Vps_Assets_Dependencies();
        $indent = str_repeat(' ', 8);
        $ret = '';
        foreach ($dep->getAssetUrls($type, 'css') as $file) {
            $ret .= "$indent<link rel=\"stylesheet\" type=\"text/css\" href=\"$file\" />\n";
        }
        foreach ($dep->getAssetUrls($type, 'printcss') as $file) {
            $ret .= "$indent<link rel=\"stylesheet\" type=\"text/css\" href=\"$file\" ";
            if (!Zend_Registry::get('config')->debug->assets->usePrintCssForAllMedia) {
                $ret .= "media=\"print\" ";
            }
            $ret .= "/>\n";
        }
        foreach ($dep->getAssetUrls($type, 'js') as $file) {
            $ret .= "$indent<script type=\"text/javascript\" src=\"$file\"></script>\n";
        }
        return $ret;
    }
}
