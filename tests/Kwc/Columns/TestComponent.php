<?php
class Kwc_Columns_TestComponent extends Kwc_Columns_Component
{
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings($parentComponentClass);
        $ret['generators']['child']['component'] = 'Kwc_Columns_TestComponent_Column';
        $ret['ownModel'] = 'Kwc_Columns_TestModel';
        $ret['childModel'] = 'Kwc_Columns_TestColumnsModel';
        return $ret;
    }
}
