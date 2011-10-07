<?php
class Vps_Component_Cache_Chained_Root extends Vpc_Root_TrlRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['title']);
        $ret['generators']['master']['component'] = 'Vps_Component_Cache_Chained_Master_Component';
        $ret['generators']['chained']['component'] = 'Vps_Component_Cache_Chained_Chained_Component.Vps_Component_Cache_Chained_Master_Component';
        $ret['childModel'] = new Vpc_Root_TrlRoot_Model(array(
            'master' => 'Master',
            'slave' => 'Slave'
        ));
        return $ret;
    }
}
