<?php
class Vps_Assets_WithComponents_Root_Component extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators'] = array();
        $ret['generators']['foo']['class'] = 'Vps_Component_Generator_Static';
        $ret['generators']['foo']['component'] = 'Vps_Assets_WithComponents_Component1_Component';
        return $ret;
    }
}
