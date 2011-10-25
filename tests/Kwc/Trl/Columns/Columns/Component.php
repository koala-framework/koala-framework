<?php
class Kwc_Trl_Columns_Columns_Component extends Kwc_Columns_Component
{
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings($parentComponentClass);
        $ret['generators']['child']['component'] = 'Kwc_Trl_Columns_Columns_Column_Component';
        $ret['ownModel'] = 'Kwc_Trl_Columns_Columns_Model';
        $ret['childModel'] = 'Kwc_Trl_Columns_Columns_ColumnsModel';
        return $ret;
    }
}
