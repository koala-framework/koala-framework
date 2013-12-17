<?php
class Kwc_Trl_Headline_Headline_Component extends Kwc_Basic_Headline_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['ownModel'] = 'Kwc_Trl_Headline_Headline_Model';
        return $ret;
    }
}
