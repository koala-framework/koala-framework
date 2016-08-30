<?php
class Kwc_Trl_Table_Table_Trl_Component extends Kwc_Basic_Table_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['childModel'] = 'Kwc_Trl_Table_Table_Trl_TrlModel';
        return $ret;
    }
}
