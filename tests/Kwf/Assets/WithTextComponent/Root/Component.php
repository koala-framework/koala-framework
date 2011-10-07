<?php
class Vps_Assets_WithTextComponent_Root_Component extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators'] = array();
        $ret['generators']['foo']['class'] = 'Vps_Component_Generator_Static';
        $ret['generators']['foo']['component'] = 'Vpc_Basic_Text_Component';
        return $ret;
    }
}
