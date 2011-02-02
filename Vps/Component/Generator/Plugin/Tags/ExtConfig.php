<?php
class Vps_Component_Generator_Plugin_Tags_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('vps.assigngrid', null);
        $config['gridAssignedControllerUrl'] = $this->getControllerUrl();
        $config['gridDataControllerUrl'] = $this->getControllerUrl('Tags');
        return array('tags' => $config);
    }
}
