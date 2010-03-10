<?php
class Vpc_Trl_Columns_Columns_Component extends Vpc_Columns_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['columns']['model'] = 'Vpc_Trl_Columns_Columns_ColumnsModel';
        $ret['generators']['columns']['component'] = 'Vpc_Trl_Columns_Columns_Column_Component';
        $ret['ownModel'] = 'Vpc_Trl_Columns_Columns_Model';
        return $ret;
    }
}
