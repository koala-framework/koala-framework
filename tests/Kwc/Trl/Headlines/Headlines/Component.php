<?php
class Kwc_Trl_Headlines_Headlines_Component extends Kwc_Basic_Headlines_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Trl_Headlines_Headlines_Model';
        return $ret;
    }
}
