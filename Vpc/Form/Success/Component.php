<?php
class Vpc_Form_Success_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard webSuccess';
        $ret['placeholder']['success'] = trlVpsStatic('The Form has been submitted successfully.');
        return $ret;
    }

}
