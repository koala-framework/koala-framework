<?php
class Kwc_Errors_AccessDenied_Component extends Kwc_Errors_Abstract_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['flags']['noIndex'] = true;
        return $ret;
    }
}
