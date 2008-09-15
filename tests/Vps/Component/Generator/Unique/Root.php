<?php
class Vps_Component_Generator_Unique_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['page']['model'] = new Vps_Model_FnF();
        $ret['generators']['box']['priority'] = 1;
//         $ret['generators']['box']['unique'] = true;
        $ret['generators']['box']['component'] = array();
        $ret['generators']['box']['component']['box'] = 'Vps_Component_Generator_Unique_Box';
        unset($ret['generators']['title']);
        return $ret;
    }
}
