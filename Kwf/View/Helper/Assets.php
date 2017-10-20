<?php
class Kwf_View_Helper_Assets
{
    public function assets($assetsPackage, $language = null, $subroot = null)
    {
        if (!$language) $language = Kwf_Trl::getInstance()->getTargetLanguage();


        $indent = str_repeat(' ', 8);
        $ret = '';

        $webpackDevServer = Kwf_Config::getValue('debug.webpackDevServer');

        if ($webpackDevServer) {
            $isRunning = true;
            if (!file_exists('temp/webpack-dev-server-pid') || posix_getpgid(file_get_contents('temp/webpack-dev-server-pid')) === false) {
                $isRunning = false;
            }

            if ($webpackDevServer === 'onDemand' && !$isRunning) {
                $webpackDevServer = false;
            }

            if ($webpackDevServer) {
                if (!$isRunning) {
                    throw new Kwf_Exception("webpack-dev-server not running, please start clear-cache-watcher");
                }
                $webpackUrl = 'http://localhost:'.Kwf_Assets_WebpackConfig::getDevServerPort();
            }
        }

        if ($webpackDevServer) {
            //fetch from dev-server, local file might not be existing
            $htmlFile = "$webpackUrl/assets/build/".$assetsPackage.'.'.$language.'.html';
        } else {
            $htmlFile = 'build/assets/'.$assetsPackage.'.'.$language.'.html';
        }

        $c = file_get_contents($htmlFile);

        $c = preg_replace('#</?head>#', '', $c);
        $c = str_replace('/assets/build/./', '/assets/build/', $c);

        $ev = new Kwf_Events_Event_CreateAssetsPackageUrls(get_class($this), $assetsPackage, $subroot);
        Kwf_Events_Dispatcher::fireEvent($ev);
        if ($ev->prefix) {
            $c = str_replace('/assets/build/', $ev->prefix.'/assets/build/', $c);
        }
        $c = preg_replace('#<script #', '<script data-kwf-unique-prefix="'. Kwf_Config::getValue('application.uniquePrefix') .'" data-base-url="'.Kwf_Config::getValue('server.baseUrl').'" ', $c, 1);

        $ret .= $c;
        return $ret;
    }
}
