<?php
class Vpc_News_Directory_Trl_Component extends Vpc_Directories_Item_Directory_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Vpc_News_Directory_Trl_Model';
        return $ret;
    }
}
