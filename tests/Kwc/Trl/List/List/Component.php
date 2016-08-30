<?php
class Kwc_Trl_List_List_Component extends Kwc_Abstract_List_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component'] = 'Kwc_Trl_List_List_Child_Component';
        $ret['childModel'] = 'Kwc_Trl_List_List_TestModel';
        $ret['ownModel'] = 'Kwc_Trl_List_List_TestOwnModel';
        return $ret;
    }
}
