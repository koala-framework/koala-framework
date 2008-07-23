<?php
class Vpc_Formular_Success_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        return $ret;
    }

}
