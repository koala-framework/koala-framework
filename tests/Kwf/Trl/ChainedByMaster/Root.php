<?php
class Kwf_Trl_ChainedByMaster_Root extends Kwc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['childModel'] = new Kwc_Trl_RootModel(array(
            'de' => 'Deutsch',
            'en' => 'English'
        ));
        $ret['generators']['master']['component'] = 'Kwf_Trl_ChainedByMaster_Master_Component';
        $ret['generators']['chained']['component'] = 'Kwf_Trl_ChainedByMaster_Chained_Component.Kwf_Trl_ChainedByMaster_Master_Component';

        $ret['generators']['box'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => array(
                'switchLanguage' => 'Kwc_Box_SwitchLanguage_Component'
            ),
            'inherit' => true,
            'priority' => 0
        );
        return $ret;
    }
}
