<?php
class Vps_View_Helper_Assets
{
    public function assets($type, $section = 'web')
    {
        $dep = new Vps_Assets_Dependencies();
        $indent = str_repeat(' ', 8);
        $ret = '';
        foreach ($dep->getAssetUrls($type, 'css', $section) as $file) {
            $ret .= "$indent<link rel=\"stylesheet\" type=\"text/css\" href=\"$file\" />\n";
        }
        foreach ($dep->getAssetUrls($type, 'printcss', $section) as $file) {
            $ret .= "$indent<link rel=\"stylesheet\" type=\"text/css\" href=\"$file\" ";
            if (!Zend_Registry::get('config')->debug->assets->usePrintCssForAllMedia) {
                $ret .= "media=\"print\" ";
            }
            $ret .= "/>\n";
        }
        foreach ($dep->getAssetUrls($type, 'js', $section) as $file) {
            $ret .= "$indent<script type=\"text/javascript\" src=\"$file\"></script>\n";
        }
        return $ret;
    }
}
