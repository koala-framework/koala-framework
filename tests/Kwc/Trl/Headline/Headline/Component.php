<?php
class Kwc_Trl_Headline_Headline_Component extends Kwc_Basic_Headline_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['ownModel'] = 'Kwc_Trl_Headline_Headline_Model';
        return $ret;
    }
}
