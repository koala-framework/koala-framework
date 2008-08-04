<?php
class Vpc_Formular_Success_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard';
        $ret['placeholder']['success'] = trlVps('The Form has been submitted successfully.');
        return $ret;
    }

}
