<?php
class Vps_View_Helper_DebugData 
{
    public function debugData()
    {
        $ret = '';
        $config = Zend_Registry::get('config')->debug;
        if ($config->menu || !$config->errormail) {
            $indent = str_repeat(' ', 8);
            $js = $config->assets->js;
            $css = $config->assets->css;
            $autoClearCache = $config->autoClearAssetsCache;
            $sessionAssets = new Zend_Session_Namespace('debugAssets');
            if (isset($sessionAssets->js)) {
                $js = $sessionAssets->js;
            }
            if (isset($sessionAssets->css)) {
                $css = $sessionAssets->css;
            }
            if (isset($sessionAssets->autoClearCache)) {
                $autoClearCache = $sessionAssets->autoClearCache;
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
        return $ret;
    }
}
