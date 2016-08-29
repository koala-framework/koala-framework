<?php
class Kwc_Trl_List_List_Trl_Component extends Kwc_Abstract_List_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Kwc_Trl_List_List_Trl_TestModel';
        return $ret;
    }
}
