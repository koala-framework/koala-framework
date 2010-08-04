<?php
class Vps_Component_Abstract_ExtConfig_Form extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $ret = array(
            'form' => $this->_getStandardConfig('vps.autoform')
        );
        return $ret;
    }
}
