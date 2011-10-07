<?php
class Vpc_Advanced_SearchEngineReferer_Referer2_Component extends Vpc_Advanced_SearchEngineReferer_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['childModel'] = 'Vpc_Advanced_SearchEngineReferer_Referer2_Model';
        return $ret;
    }
}

