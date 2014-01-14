<?php
class Kwc_Columns_Trl_Component extends Kwc_Abstract_List_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = Kwc_Chained_Trl_Component::getSettings($masterComponentClass);
        unset($ret['childModel']);
        $ret['generators']['child']['class'] = 'Kwc_Chained_Trl_Generator';
        $ret['extConfig'] = 'Kwc_Abstract_List_Trl_ExtConfigList';
        return $ret;
    }
}