<?php
class Kwc_Basic_Text_Link_TestComponent extends Kwc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Text_Link_TestModel';
        $ret['generators']['child']['component'] = array();
        $ret['generators']['child']['component']['extern'] = 'Kwc_Basic_Text_Link_Extern_TestComponent';
        $ret['generators']['child']['component']['mail'] = 'Kwc_Basic_Text_Link_Mail_TestComponent';
        $ret['generators']['child']['component']['intern'] = 'Kwc_Basic_Text_Link_Intern_TestComponent';
        return $ret;
    }
}
