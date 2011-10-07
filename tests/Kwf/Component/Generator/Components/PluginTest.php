<?php
class Kwf_Component_Generator_Components_PluginTest extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['plugins'] = array(
            'Kwf_Component_Generator_Components_Plugin'
        );
        return $ret;
    }
}