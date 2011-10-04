<?php
class Vpc_Basic_TextSessionModel_Link_Extern_Component extends Vpc_Basic_LinkTag_Extern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Vpc_Basic_TextSessionModel_Link_Extern_TestModel';
        return $ret;
    }
}
