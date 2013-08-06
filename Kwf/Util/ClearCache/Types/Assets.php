<?php
class Kwf_Util_ClearCache_Types_Assets extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _clearCache($options)
    {
        Kwf_Assets_Cache::getInstance()->clean();
    }

    protected function _refreshCache($options)
    {
        $loader = new Kwf_Assets_Loader();
        $loader->getDependencies()->getMaxFileMTime(); //this is expensive and gets cached in filesystem

        $webCodeLanguage = Kwf_Registry::get('config')->webCodeLanguage;
        $_SERVER['HTTP_ACCEPT_ENCODING'] = 'gzip';
        $assets = Kwf_Registry::get('config')->assets->toArray();
        $assetTypes = array();
        foreach ($assets as $assetsType => $v) {
            if ($assetsType == 'dependencies') continue;
            $this->_output($assetsType.' ');
            $urls = $loader->getDependencies()->getAssetUrls($assetsType, 'js', 'web', Kwf_Component_Data_Root::getComponentClass(), $webCodeLanguage);
            $urls = array_merge($urls, $loader->getDependencies()->getAssetUrls($assetsType, 'css', 'web', Kwf_Component_Data_Root::getComponentClass(), $webCodeLanguage));
            foreach ($urls as $url) {
                if (substr($url, 0, 7) == 'http://' || substr($url, 0, 8) == 'https://') {
                    continue;
                }
                $url = preg_replace('#^'.Kwf_Setup::getBaseUrl().'/assets/#', '', $url);
                $url = preg_replace('#\\?v=\d+(&t=\d+)?$#', '', $url);
                $loader->getFileContents($url);
            }
        }
    }

    public function getTypeName()
    {
        return 'assets';
    }
    public function doesRefresh() { return true; }
    public function doesClear() { return true; }
}
