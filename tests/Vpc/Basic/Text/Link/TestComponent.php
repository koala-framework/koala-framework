<?php
class Vpc_Basic_Text_Link_TestComponent extends Vpc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_Text_Link_TestModel';
        $ret['generators']['link']['component'] = array();
        $ret['generators']['link']['component']['extern'] = 'Vpc_Basic_Text_Link_Extern_TestComponent';
        $ret['generators']['link']['component']['mail'] = 'Vpc_Basic_Text_Link_Mail_TestComponent';
        $ret['generators']['link']['component']['intern'] = 'Vpc_Basic_Text_Link_Intern_TestComponent';
        return $ret;
    }
}
