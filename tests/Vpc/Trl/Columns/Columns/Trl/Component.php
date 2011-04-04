<?php
class Vpc_Trl_Columns_Columns_Trl_Component extends Vpc_Columns_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Vpc_Trl_Columns_Columns_Trl_ColumnsTrlModel';
        return $ret;
    }
}
