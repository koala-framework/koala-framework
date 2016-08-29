<?php
class Kwc_Trl_List_List_Child_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id'
        ));
        return $ret;
    }
}
