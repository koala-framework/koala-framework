<?php
class Kwf_Component_Cache_Chained_Root extends Kwc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['generators']['master']['component'] = 'Kwf_Component_Cache_Chained_StartMaster_Component';
        $ret['generators']['chained']['component'] = 'Kwf_Component_Cache_Chained_StartChained_Component.Kwf_Component_Cache_Chained_StartMaster_Component';
        $ret['childModel'] = new Kwc_Trl_RootModel(array(
            'master' => 'Master',
            'slave' => 'Slave'
        ));
        return $ret;
    }
}
