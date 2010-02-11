<?php
class Vpc_Root_LanguageRoot_TestComponent extends Vpc_Root_LanguageRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['language']['component'] = array();
        $ret['generators']['language']['component']['de'] = 'Vpc_Root_LanguageRoot_Language_TestComponent';
        $ret['generators']['language']['component']['en'] = 'Vpc_Root_LanguageRoot_Language_TestComponent';
        $ret['generators']['language']['component']['fr'] = 'Vpc_Root_LanguageRoot_Language_TestComponent';
        return $ret;
    }
}
