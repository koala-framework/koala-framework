<?php
class Kwc_Errors_NotFound_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = false;
        return $ret;
    }
    
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['requestUri'] = $_SERVER['REQUEST_URI']; // TODO
        return $ret;
    }
}
