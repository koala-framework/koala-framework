<?php
class Vpc_Basic_Table_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $settings = $this->_getStandardConfig('vps.autoform', 'Settings', trlVps('Settings'), new Vps_Asset('wrench_orange'));
        $table = $this->_getStandardConfig('vps.autogrid', 'Index', trlVps('Table'), new Vps_Asset('wrench'));
        $table['renderAlways'] = true;
        $table['insertNewRowAtBottom'] = true;
        return array(
            'settings' => $settings,
            'table' => $table
        );
    }
}
