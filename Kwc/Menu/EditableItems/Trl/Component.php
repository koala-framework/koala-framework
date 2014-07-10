<?php
class Kwc_Menu_EditableItems_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['extConfig'] = 'Kwc_Menu_EditableItems_ExtConfig';
        return $ret;
    }
}
