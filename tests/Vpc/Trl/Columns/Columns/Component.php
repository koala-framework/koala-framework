<?php
class Vpc_Trl_Columns_Columns_Component extends Vpc_Columns_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Trl_Columns_Columns_Column_Component';
        $ret['ownModel'] = 'Vpc_Trl_Columns_Columns_Model';
        $ret['childModel'] = 'Vpc_Trl_Columns_Columns_ColumnsModel';
        return $ret;
    }
}
