<?php
class Vps_Component_Abstract_ExtConfig_Grid extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $ret = array(
            'grid' => $this->_getStandardConfig('vps.autogrid')
        );
        return $ret;
    }
}
