<?php
class Kwf_View_Helper_Assets
{
    public function assets(Kwf_Assets_Package $assetsPackage, $language = null)
    {
        if (!$language) $language = Kwf_Trl::getInstance()->getTargetLanguage();

        $indent = str_repeat(' ', 8);
        $ret = '';
        foreach ($assetsPackage->getPackageUrls('text/css', $language) as $file) {
            $ret .= "$indent<link rel=\"stylesheet\" type=\"text/css\" href=\"".htmlspecialchars($file)."\" />\n";
        }
        foreach ($assetsPackage->getPackageUrls('text/css; media=print', $language) as $file) {
            $ret .= "$indent<link rel=\"stylesheet\" type=\"text/css\" href=\"".htmlspecialchars($file)."\" ";
            if (!Kwf_Config::getValue('debug.assets.usePrintCssForAllMedia')) {
                $ret .= "media=\"print\" ";
            }
            $ret .= "/>\n";
        }
        foreach ($assetsPackage->getPackageUrls('text/javascript', $language) as $file) {
            $ret .= "$indent<script type=\"text/javascript\" src=\"".htmlspecialchars($file)."\"></script>\n";
        }
        return $ret;
    }
}
