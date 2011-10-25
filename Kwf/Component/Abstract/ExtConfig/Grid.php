<?php
class Kwf_Component_Abstract_ExtConfig_Grid extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $ret = array(
            'grid' => $this->_getStandardConfig('kwf.autogrid')
        );
        return $ret;
    }
}
