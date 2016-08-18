<?php
class Kwc_Form_Dynamic_Form_Success_Component extends Kwc_Basic_Text_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['rootElementClass'] = 'kwfUp-webStandard webSuccess';
        $ret['componentName'] = trlKwfStatic('Text on successful submit');
        $ret['defaultText'] = trlKwfStatic('The form has been submitted successfully.');
        return $ret;
    }

}
