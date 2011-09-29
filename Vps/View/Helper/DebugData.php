<?php
class Vps_View_Helper_DebugData
{
    public function debugData()
    {
        $ret = '';
        $config = Vps_Config::getValueArray('debug');
        if ($config['menu'] || !$config['error']['log'] || Vps_Component_Data_Root::getInstance()->filename) {
            $indent = str_repeat(' ', 8);
            $ret .= "<script type=\"text/javascript\">\n";
            $ret .=$indent."if (typeof Vps == 'undefined') Vps = {};\n";
            $ret .=$indent."Vps.Debug = {};\n";
            $ret .=$indent.'Vps.Debug.displayErrors = '.(!$config['error']['log'] ? 'true' : 'false').";\n";
            if (Vps_Component_Data_Root::getInstance()->filename) {
                $ret .=$indent.'Vps.Debug.rootFilename = \'/'.Vps_Component_Data_Root::getInstance()->filename."';\n";
            }
            $session = new Zend_Session_Namespace('debug');
            if ($session->enable) {
                $js = $config['assets']['js'];
                $css = $config['assets']['css'];
                $autoClearCache = $config['autoClearAssetsCache'];
                if (isset($session->assetsJs)) {
                    $js = $session->assetsJs;
                }
                if (isset($session->assetsCss)) {
                    $css = $session->assetsCss;
                }
                if (isset($session->autoClearAssetsCache)) {
                    $autoClearCache = $session->autoClearAssetsCache;
                }
                $ret .=$indent.'Vps.Debug.showMenu = '.($config['menu'] ? 'true' : 'false').";\n";
                $ret .=$indent.'Vps.Debug.js = '.($js ? 'true' : 'false').";\n";
                $ret .=$indent.'Vps.Debug.css = '.($css ? 'true' : 'false').";\n";
                $ret .=$indent.'Vps.Debug.autoClearCache = '.($autoClearCache ? 'true' : 'false').";\n";
                $ret .=$indent.'Vps.Debug.querylog = '.($config['querylog'] ? 'true' : 'false').";\n";
                $ret .=$indent.'Vps.Debug.requestNum = \''.Zend_Registry::get('requestNum')."';\n";
            }
            $ret .=$indent."</script>\n";
        }
        return $ret;
    }
}
