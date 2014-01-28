<?php
class Kwc_Trl_Columns_Columns_Component extends Kwc_Columns_Component
{
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings($parentComponentClass);
        $ret['childModel'] = 'Kwc_Trl_Columns_Columns_TestChildModel';
        $ret['ownModel'] = 'Kwc_Trl_Columns_Columns_TestModel';
        $ret['generators']['child']['component'] = 'Kwc_Trl_Columns_Columns_Column_Component';
        return $ret;
    }
}
