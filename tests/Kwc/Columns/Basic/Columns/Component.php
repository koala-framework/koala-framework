<?php
class Kwc_Columns_Basic_Columns_Component extends Kwc_Columns_Component
{
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings($parentComponentClass);
        $ret['childModel'] = 'Kwc_Columns_Basic_Columns_TestChildModel';
        $ret['ownModel'] = 'Kwc_Columns_Basic_Columns_TestModel';
        $ret['generators']['child']['component'] = 'Kwc_Columns_Basic_Columns_Column_Component';
        return $ret;
    }
}
