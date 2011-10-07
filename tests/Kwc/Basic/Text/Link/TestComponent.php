<?php
class Vpc_Basic_Text_Link_TestComponent extends Vpc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Text_Link_TestModel';
        $ret['generators']['child']['component'] = array();
        $ret['generators']['child']['component']['extern'] = 'Vpc_Basic_Text_Link_Extern_TestComponent';
        $ret['generators']['child']['component']['mail'] = 'Vpc_Basic_Text_Link_Mail_TestComponent';
        $ret['generators']['child']['component']['intern'] = 'Vpc_Basic_Text_Link_Intern_TestComponent';
        return $ret;
    }
}
