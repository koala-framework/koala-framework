<?php
class Kwc_Legacy_Columns_Basic_TestComponent extends Kwc_Legacy_Columns_Component
{
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings($parentComponentClass);
        $ret['generators']['child']['component'] = 'Kwc_Legacy_Columns_Basic_TestComponent_Column';
        $ret['ownModel'] = 'Kwc_Legacy_Columns_Basic_TestModel';
        $ret['childModel'] = 'Kwc_Legacy_Columns_Basic_TestColumnsModel';
        return $ret;
    }
}
