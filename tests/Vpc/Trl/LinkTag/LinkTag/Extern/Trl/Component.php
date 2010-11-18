<?php
class Vpc_Trl_LinkTag_LinkTag_Extern_Trl_Component extends Vpc_Basic_LinkTag_Extern_Trl_Component
{
    public static function getSettings($masterComponent)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['ownModel'] = 'Vpc_Trl_LinkTag_LinkTag_Extern_Trl_TestModel';
        return $ret;
    }
}
