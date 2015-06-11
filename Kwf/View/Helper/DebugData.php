<?php
class Kwf_View_Helper_DebugData
{
    public function debugData()
    {
        $ret = '';
        $config = Kwf_Config::getValueArray('debug');
        if ($config['benchmark'] || $config['menu'] || !$config['error']['log'] || (Kwf_Component_Data_Root::getInstance() && Kwf_Component_Data_Root::getInstance()->filename)) {
            $indent = str_repeat(' ', 8);
            $ret .= "<script type=\"text/javascript\">\n";
            $kwf = 'Kwf';
            if ($uniquePrefix = Kwf_Config::getValue('application.uniquePrefix')) {
                $ret .=$indent."if (typeof $uniquePrefix == 'undefined') $uniquePrefix = {};\n";
                $kwf = $uniquePrefix.'.'.$kwf;
            }
            $ret .=$indent."if (typeof $kwf == 'undefined') $kwf = {};\n";
            $ret .=$indent."$kwf.Debug = {};\n";
            $ret .=$indent."$kwf.Debug.displayErrors = ".(!$config['error']['log'] ? 'true' : 'false').";\n";
            $ret .=$indent."$kwf.Debug.benchmark = ".($config['benchmark'] ? 'true' : 'false').";\n";
            if (Kwf_Component_Data_Root::getInstance() && Kwf_Component_Data_Root::getInstance()->filename) {
                $ret .=$indent."$kwf.Debug.rootFilename = '/".Kwf_Component_Data_Root::getInstance()->filename."';\n";
            }
            $ret .=$indent."</script>\n";
        }
        return $ret;
    }
}
