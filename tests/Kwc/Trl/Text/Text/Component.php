<?php
class Kwc_Trl_Text_Text_Component extends Kwc_Basic_Text_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Trl_Text_Text_TestModel';
        return $ret;
    }
}
