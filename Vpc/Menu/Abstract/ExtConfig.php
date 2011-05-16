<?php
class Vpc_Menu_Abstract_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('vpc.menu.panel');
        $config['formControllerUrl'] = $this->getControllerUrl('Form');
        return array(
            'form' => $config
        );
    }
}