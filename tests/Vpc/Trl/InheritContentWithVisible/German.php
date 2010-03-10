<?php
class Vpc_Trl_InheritContentWithVisible_German extends Vpc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators'] = array();
        $ret['editComponents'] = array();

        $ret['generators']['test'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_InheritContentWithVisible_Test_Component',
            'name' => 'test',
        );
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Trl_InheritContentWithVisible_Box_Component',
            'inherit' => true
        );
        return $ret;
    }
}
