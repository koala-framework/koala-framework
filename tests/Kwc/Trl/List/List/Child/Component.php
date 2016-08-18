<?php
class Kwc_Trl_List_List_Child_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentName'] = 'Foo';
        $ret['ownModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id'
        ));
        return $ret;
    }
}