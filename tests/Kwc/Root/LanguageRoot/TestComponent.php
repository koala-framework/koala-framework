<?php
class Kwc_Root_LanguageRoot_TestComponent extends Kwc_Root_LanguageRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['language']['component'] = array();
        $ret['generators']['language']['component']['de'] = 'Kwc_Root_LanguageRoot_Language_TestComponent';
        $ret['generators']['language']['component']['en'] = 'Kwc_Root_LanguageRoot_Language_TestComponent';
        $ret['generators']['language']['component']['fr'] = 'Kwc_Root_LanguageRoot_Language_TestComponent';
        return $ret;
    }
}
