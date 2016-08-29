<?php
class Kwc_Trl_LinkTag_LinkTag_Extern_Component extends Kwc_Basic_LinkTag_Extern_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Trl_LinkTag_LinkTag_Extern_TestModel';
        return $ret;
    }
}
