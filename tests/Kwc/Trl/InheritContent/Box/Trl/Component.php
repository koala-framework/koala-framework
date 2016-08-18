<?php
class Kwc_Trl_InheritContent_Box_Trl_Component extends Kwc_Box_InheritContent_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        $ret['ownModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id' => 'root-en-box', 'visible' => true)
            )
        ));
        return $ret;
    }
}
