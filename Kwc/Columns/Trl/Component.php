<?php
class Kwc_Columns_Trl_Component extends Kwc_Abstract_List_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['generators']['child']['class'] = 'Kwc_Chained_Abstract_Generator';
        unset($ret['childModel']);
        return $ret;
    }
}
