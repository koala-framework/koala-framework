<?php
class Kwc_Columns_Trl_Component extends Kwc_Abstract_List_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['extConfig'] = 'Kwc_Abstract_List_Trl_ExtConfigFullSizeEdit';
        $ret['generators']['child']['class'] = 'Kwc_Columns_Trl_Generator';
        return $ret;
    }
}
