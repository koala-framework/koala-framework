<?php
class Kwc_Form_Success_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['rootElementClass'] = 'kwfUp-webStandard webSuccess';
        $ret['placeholder']['success'] = trlKwfStatic('The form has been submitted successfully.');
        return $ret;
    }

}
