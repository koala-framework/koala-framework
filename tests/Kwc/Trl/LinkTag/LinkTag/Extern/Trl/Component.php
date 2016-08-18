<?php
class Kwc_Trl_LinkTag_LinkTag_Extern_Trl_Component extends Kwc_Basic_LinkTag_Extern_Trl_Component
{
    public static function getSettings($masterComponent = null)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['ownModel'] = 'Kwc_Trl_LinkTag_LinkTag_Extern_Trl_TestModel';
        return $ret;
    }
}
