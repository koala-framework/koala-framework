<?php
class Vps_View_Helper_DebugData
{
    public function debugData()
    {
        $ret = '';
        $config = Zend_Registry::get('config')->debug;
        if ($config->menu || !$config->error->log) {
            $session = new Zend_Session_Namespace('debug');
            if ($session->enable) {
                $indent = str_repeat(' ', 8);
                $js = $config->assets->js;
                $css = $config->assets->css;
                $autoClearCache = $config->autoClearAssetsCache;
                if (isset($session->assetsJs)) {
                    $js = $session->assetsJs;
                }
                if (isset($session->assetsCss)) {
                    $css = $session->assetsCss;
                }
                if (isset($session->autoClearAssetsCache)) {
                    $autoClearCache = $session->autoClearAssetsCache;
                }
                $ret .= "<script type=\"text/javascript\">\n";
                $ret .=$indent.'Vps.Debug.displayErrors = '.(!$config->errormail ? 'true' : 'false').";\n";
                $ret .=$indent.'Vps.Debug.showMenu = '.($config->menu ? 'true' : 'false').";\n";
                $ret .=$indent.'Vps.Debug.js = '.($js ? 'true' : 'false').";\n";
                $ret .=$indent.'Vps.Debug.css = '.($css ? 'true' : 'false').";\n";
                $ret .=$indent.'Vps.Debug.autoClearCache = '.($autoClearCache ? 'true' : 'false').";\n";
                $ret .=$indent.'Vps.Debug.querylog = '.($config->querylog ? 'true' : 'false').";\n";
                $ret .=$indent.'Vps.Debug.requestNum = \''.Zend_Registry::get('requestNum')."';\n";
                $ret .=$indent."</script>\n";
            }
        }
        return $ret;
    }
}
