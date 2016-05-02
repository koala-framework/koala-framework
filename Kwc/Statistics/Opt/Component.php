<?php
class Kwc_Statistics_Opt_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['componentName'] = trlKwfStatic('Cookie Opt In / Opt Out');
        return $ret;
    }
}
