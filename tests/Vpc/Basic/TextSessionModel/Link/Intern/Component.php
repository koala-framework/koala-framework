<?php
class Vpc_Basic_TextSessionModel_Link_Intern_Component extends Vpc_Basic_LinkTag_Intern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_TextSessionModel_Link_Intern_TestModel';
        return $ret;
    }
}
