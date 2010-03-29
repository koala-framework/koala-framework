<?php
class Vpc_Trl_List_List_Child_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = 'Foo';
        $ret['ownModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id'
        ));
        return $ret;
    }
}