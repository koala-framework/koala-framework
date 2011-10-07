<?php
class Vps_Component_Generator_Components_PluginTest extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['plugins'] = array(
            'Vps_Component_Generator_Components_Plugin'
        );
        return $ret;
    }
}