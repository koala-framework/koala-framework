<?php
class Kwc_Basic_Text_Link_Extern_TestComponent extends Kwc_Basic_LinkTag_Extern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Basic_Text_Link_Extern_TestModel';
        return $ret;
    }
}
