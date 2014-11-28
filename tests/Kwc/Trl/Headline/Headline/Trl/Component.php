<?php
class Kwc_Trl_Headline_Headline_Trl_Component extends Kwc_Basic_Headline_Trl_Component
{
    public static function getSettings($mcc)
    {
        $ret = parent::getSettings($mcc);
        $ret['ownModel'] = 'Kwc_Trl_Headline_Headline_Trl_Model';
        return $ret;
    }
}
