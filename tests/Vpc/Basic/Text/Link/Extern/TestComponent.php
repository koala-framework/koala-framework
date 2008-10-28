<?php
class Vpc_Basic_Text_Link_Extern_TestComponent extends Vpc_Basic_LinkTag_Extern_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Basic_Text_Link_Extern_TestModel';
        return $ret;
    }
}
