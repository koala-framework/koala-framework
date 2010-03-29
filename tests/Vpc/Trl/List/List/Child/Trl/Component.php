<?php
class Vpc_Trl_List_List_Child_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id'
        ));
        return $ret;
    }
}
