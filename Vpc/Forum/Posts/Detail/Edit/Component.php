<?php
class Vpc_Forum_Posts_Detail_Edit_Component extends Vpc_Posts_Detail_Edit_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['breadCrumbs'] = 'Vpc_Menu_BreadCrumbs_Component';
        return $ret;
    }

}
