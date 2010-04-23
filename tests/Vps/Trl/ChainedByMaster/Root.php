<?php
class Vps_Trl_ChainedByMaster_Root extends Vpc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['childModel'] = new Vpc_Trl_RootModel(array(
            'de' => 'Deutsch',
            'en' => 'English'
        ));
        $ret['generators']['master']['component'] = 'Vps_Trl_ChainedByMaster_Master_Component';
        $ret['generators']['chained']['component'] = 'Vpc_Root_TrlRoot_Chained_Component.Vps_Trl_ChainedByMaster_Master_Component';

        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => array(
                'switchLanguage' => 'Vpc_Box_SwitchLanguage_Component'
            ),
            'inherit' => true,
            'priority' => 0
        );
        return $ret;
    }
}
