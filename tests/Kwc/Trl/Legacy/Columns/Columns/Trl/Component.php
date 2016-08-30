<?php
class Kwc_Trl_Legacy_Columns_Columns_Trl_Component extends Kwc_Legacy_Columns_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Kwc_Trl_Legacy_Columns_Columns_Trl_ColumnsTrlModel';
        return $ret;
    }
}
