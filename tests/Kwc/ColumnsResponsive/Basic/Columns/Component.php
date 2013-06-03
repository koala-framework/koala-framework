<?php
class Kwc_ColumnsResponsive_Basic_Columns_Component extends Kwc_ColumnsResponsive_Component
{
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings($parentComponentClass);
        $ret['ownModel'] = 'Kwc_ColumnsResponsive_Basic_Columns_TestModel';
        $ret['generators']['child']['component'] = 'Kwc_ColumnsResponsive_Basic_Columns_Column_Component';
        return $ret;
    }
}
