<?php
class Vpc_Advanced_SearchEngineReferer_Referer_Component extends Vpc_Advanced_SearchEngineReferer_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['modelname'] = 'Vpc_Advanced_SearchEngineReferer_Referer_Model';
        return $ret;
    }
}

