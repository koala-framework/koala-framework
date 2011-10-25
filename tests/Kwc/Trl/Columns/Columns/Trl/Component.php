<?php
class Kwc_Trl_Columns_Columns_Trl_Component extends Kwc_Columns_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Kwc_Trl_Columns_Columns_Trl_ColumnsTrlModel';
        return $ret;
    }
}
