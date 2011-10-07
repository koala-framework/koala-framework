<?php
class Vpc_Trl_InheritContentWithVisible_Test_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['test2'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vpc_Trl_InheritContentWithVisible_Test_Test2_Component',
            'name' => 'test2'
        );
        return $ret;
    }
}
