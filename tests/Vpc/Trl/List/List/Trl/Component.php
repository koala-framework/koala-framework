<?php
class Vpc_Trl_List_List_Trl_Component extends Vpc_Abstract_List_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Vpc_Trl_List_List_Trl_TestModel';
        return $ret;
    }
}
