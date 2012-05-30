<?php
class Kwc_Trl_Headlines_Headlines_Trl_Component extends Kwc_Basic_Headlines_Trl_Component
{
    public static function getSettings($mcc)
    {
        $ret = parent::getSettings($mcc);
        $ret['ownModel'] = 'Kwc_Trl_Headlines_Headlines_Trl_Model';
        return $ret;
    }
}
