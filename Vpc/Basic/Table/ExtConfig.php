<?php
class Vpc_Basic_Table_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    private function _getConfig()
    {
        $settings = $this->_getStandardConfig('vps.autoform', 'Settings', trlVps('Settings'), Vps_Asset('wrench_orange'));
        $table = $this->_getStandardConfig('vps.autogrid', 'Index', trlVps('Table'), Vps_Asset('wrench'));
        return array(
            'settings' => $settings,
            'table' => $table
        );
    }
}
