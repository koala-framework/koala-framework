<?php
class Vpc_Root_TrlRoot_TestComponent extends Vpc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['master']['component'] = 'Vpc_Root_TrlRoot_Master_TestComponent';
        $ret['generators']['chained']['component'] = 'Vpc_Root_TrlRoot_Slave_Component.Vpc_Root_TrlRoot_Master_TestComponent';
        $ret['childModel'] = new Vpc_Trl_RootModel(array(
            'de' => 'Deutsch',
            'en' => 'English'
        ));
        return $ret;
    }
}
