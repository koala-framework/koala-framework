<?php
class Vps_Component_Cache_ComponentLink_DbIdShortcut_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['childpages'] = array(
            'class' => 'Vps_Component_Cache_ComponentLink_DbIdShortcut_Generator',
            'component' => 'Vpc_Basic_Empty_Component',
            'dbIdShortcut' => 'foo_'
        );
        $ret['childModel'] = 'Vps_Component_Cache_ComponentLink_DbIdShortcut_Model';
        return $ret;
    }
}
