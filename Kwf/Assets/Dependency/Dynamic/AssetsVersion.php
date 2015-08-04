<?php
class Kwf_Assets_Dependency_Dynamic_AssetsVersion extends Kwf_Assets_Dependency_Abstract
{
    public function getMimeType()
    {
        return 'text/javascript';
    }

    public function getContents($language)
    {
        $ret = "if (typeof Kwf == 'undefined') Kwf = {};".
            "Kwf.application = { assetsVersion: '".Kwf_Assets_Dispatcher::getAssetsVersion()."' };\n";
        return $ret;
    }

    public function usesLanguage()
    {
        return false;
    }
}
