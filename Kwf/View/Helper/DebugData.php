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
            $ret .=$indent."</script>\n";
        }
        return $ret;
    }
}
