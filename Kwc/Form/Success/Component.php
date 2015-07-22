<?php
class Kwc_Form_Success_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['rootElementClass'] = 'kwfup-webStandard webSuccess';
        $ret['placeholder']['success'] = trlKwfStatic('The form has been submitted successfully.');
        return $ret;
    }

}
