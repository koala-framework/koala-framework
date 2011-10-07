<?php
class Vpc_Advanced_SocialBookmarks_Trl_Component extends Vpc_Chained_Trl_MasterAsChild_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['editComponents'][] = 'child'; //kann das vielleicht im parent gemacht werden?
        return $ret;
    }
}