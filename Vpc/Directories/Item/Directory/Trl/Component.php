<?php
class Vpc_Directories_Item_Directory_Trl_Component extends Vpc_Directories_List_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['detail']['class'] = 'Vpc_Directories_Item_Directory_Trl_Generator';
        return $ret;
    }
}
