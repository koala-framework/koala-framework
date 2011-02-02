<?php
class Vpc_Trl_List_List_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component'] = 'Vpc_Trl_List_List_Child_Component';
        $ret['childModel'] = 'Vpc_Trl_List_List_TestModel';
        $ret['ownModel'] = 'Vpc_Trl_List_List_TestOwnModel';
        return $ret;
    }
}
