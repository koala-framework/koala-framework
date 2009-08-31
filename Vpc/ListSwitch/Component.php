<?php
class Vpc_ListSwitch_Component extends Vpc_Abstract_List_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['assets']['files'][] = 'vps/Vpc/ListSwitch/Component.js';
        $ret['assets']['dep'][] = 'ExtCore';
        $ret['generators']['child']['component'] = 'Vpc_ListSwitch_Preview_Component';
        return $ret;
    }
}
