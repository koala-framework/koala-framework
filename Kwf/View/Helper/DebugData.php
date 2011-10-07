<?php
class Kwf_View_Helper_DebugData
{
    public function debugData()
    {
        $ret = '';
        $config = Kwf_Config::getValueArray('debug');
        if ($config['menu'] || !$config['error']['log'] || (Kwf_Component_Data_Root::getInstance() && Kwf_Component_Data_Root::getInstance()->filename)) {
            $indent = str_repeat(' ', 8);
            $ret .= "<script type=\"text/javascript\">\n";
            $ret .=$indent."if (typeof Kwf == 'undefined') Kwf = {};\n";
            $ret .=$indent."Kwf.Debug = {};\n";
            $ret .=$indent.'Kwf.Debug.displayErrors = '.(!$config['error']['log'] ? 'true' : 'false').";\n";
            if (Kwf_Component_Data_Root::getInstance() && Kwf_Component_Data_Root::getInstance()->filename) {
                $ret .=$indent.'Kwf.Debug.rootFilename = \'/'.Kwf_Component_Data_Root::getInstance()->filename."';\n";
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
                $ret .=$indent.'Kwf.Debug.showMenu = '.($config['menu'] ? 'true' : 'false').";\n";
                $ret .=$indent.'Kwf.Debug.js = '.($js ? 'true' : 'false').";\n";
                $ret .=$indent.'Kwf.Debug.css = '.($css ? 'true' : 'false').";\n";
                $ret .=$indent.'Kwf.Debug.autoClearCache = '.($autoClearCache ? 'true' : 'false').";\n";
                $ret .=$indent.'Kwf.Debug.querylog = '.($config['querylog'] ? 'true' : 'false').";\n";
                $ret .=$indent.'Kwf.Debug.requestNum = \''.Zend_Registry::get('requestNum')."';\n";
            }
            $ret .=$indent."</script>\n";
        }
        return $ret;
    }
}
