<?php
class Kwc_Trl_LinkTag_LinkTag_Trl_Component extends Kwc_Basic_LinkTag_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = 'Kwc_Trl_LinkTag_LinkTag_Trl_TestModel';
        return $ret;
    }
}
