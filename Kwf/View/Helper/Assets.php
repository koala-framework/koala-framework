<?php
class Kwf_View_Helper_Assets
{
    public function assets($assetsPackage, $language = null, $subroot = null)
    {
        if (!$language) $language = Kwf_Trl::getInstance()->getTargetLanguage();

        $ev = new Kwf_Events_Event_CreateAssetsPackageUrls(get_class($this), $assetsPackage, $subroot);
        Kwf_Events_Dispatcher::fireEvent($ev);
        $prefix = $ev->prefix;

        $indent = str_repeat(' ', 8);
        $ret = '';

        $c = file_get_contents('build/assets/'.$assetsPackage.'.'.$language.'.html');

        $c = preg_replace('#</?head>#', '', $c);
        $c = str_replace('/assets/build/./', '/assets/build/', $c);

        if (Kwf_Config::getValue('debug.webpackDevServer')) {
            if (!file_exists('cache/webpack-dev-server-port')) {
                throw new Kwf_Exception("webpack-dev-server not running, please start clear-cache-watcher");
            }
            $port = file_get_contents('cache/webpack-dev-server-port');
            $host = trim(`hostname`);
            $c = str_replace('/assets/build/', "http://$host:$port/assets/build/", $c);
        }

        $ret .= $c;
        return $ret;
    }
}
