<?php
class Kwc_Root_TrlRoot_TestComponent extends Kwc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['master']['component'] = 'Kwc_Root_TrlRoot_Master_TestComponent';
        $ret['generators']['chained']['component'] = 'Kwc_Root_TrlRoot_Slave_Component.Kwc_Root_TrlRoot_Master_TestComponent';
        $ret['childModel'] = new Kwc_Trl_RootModel(array(
            'de' => 'Deutsch',
            'en' => 'English'
        ));
        return $ret;
    }
}
