<?php
class Vpc_Basic_TextSessionModel_Link_Component extends Vpc_Basic_LinkTag_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_TextSessionModel_Link_TestModel';
        $ret['generators']['child']['component'] = array();
        $ret['generators']['child']['component']['extern'] = 'Vpc_Basic_TextSessionModel_Link_Extern_Component';
        $ret['generators']['child']['component']['intern'] = 'Vpc_Basic_TextSessionModel_Link_Intern_Component';
        return $ret;
    }
}
