<?php
class Vpc_Root_TrlRoot_Component extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['category']);
        $ret['generators']['master'] = array(
            'class' => 'Vps_Component_Generator_PseudoPage_Static',
            'component' => 'Vpc_Root_TrlRoot_Master_Component',
            'name' => 'de',
        );
        $ret['generators']['slave'] = array(
            'class' => 'Vps_Component_Generator_PseudoPage_Static',
            'component' => 'Vpc_Chained_Trl_Base_Component.Vpc_Root_TrlRoot_Master_Component',
            'name' => 'en',
        );
        return $ret;
    }
}
