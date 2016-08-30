<?php
class Kwc_Advanced_SearchEngineReferer_Referer2_Component extends Kwc_Advanced_SearchEngineReferer_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwc_Advanced_SearchEngineReferer_Referer2_Model';
        return $ret;
    }
}

