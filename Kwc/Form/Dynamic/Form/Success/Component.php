<?php
class Kwc_Form_Dynamic_Form_Success_Component extends Kwc_TextImage_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['cssClass'] = 'webStandard webSuccess';
        $ret['componentName'] = trlKwfStatic('Text on successful submit');
        return $ret;
    }

}
