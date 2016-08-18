<?php
class Kwc_Trl_InheritContentWithVisible_Box_Trl_Component extends Kwc_Box_InheritContent_Trl_Component
{
    public static function getSettings($masterComponent = null)
    {
        $ret = parent::getSettings($masterComponent);
        $ret['ownModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id'=>'root-en-box', 'visible'=>1),
                array('component_id'=>'root-en_test_test2_test3-box', 'visible'=>1),
            )
        ));
        return $ret;
    }
}
