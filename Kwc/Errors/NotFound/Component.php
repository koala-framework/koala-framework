<?php
class Kwc_Errors_NotFound_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['viewCache'] = false;
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        return $ret;
    }
    
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['requestUri'] = $_SERVER['REQUEST_URI']; // TODO
        return $ret;
    }
}
