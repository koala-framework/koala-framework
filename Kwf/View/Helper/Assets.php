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
        foreach ($assetsPackage->getPackageUrls('text/css; ie8', $language) as $file) {
            $ret .= "$indent<!--[if lte IE 8]><link rel=\"stylesheet\" type=\"text/css\" href=\"".htmlspecialchars($file)."\" /><![endif]-->\n";
        }
        foreach ($assetsPackage->getPackageUrls('text/javascript', $language) as $file) {
            $ret .= "$indent<script type=\"text/javascript\" src=\"".htmlspecialchars($file)."\"></script>\n";
        }
        foreach ($assetsPackage->getPackageUrls('text/javascript; defer', $language) as $file) {
            $ret .= "<script type=\"text/javascript\">
                var se=document.createElement('script');se.type='text/javascript';se.async=true;
                se.src='".$file."';
                var s=document.getElementsByTagName('script')[0];s.parentNode.insertBefore(se,s);
            </script>";
        }
        return $ret;
    }
}
