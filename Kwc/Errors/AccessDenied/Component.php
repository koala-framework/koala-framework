<?php
class Kwc_Errors_AccessDenied_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        return $ret;
    }
}
