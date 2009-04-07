<?php
class Vpc_Misc_UrlInclude_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Url include');
        $ret['modelname'] = 'Vps_Component_FieldModel';
        return $ret;
    }
}
