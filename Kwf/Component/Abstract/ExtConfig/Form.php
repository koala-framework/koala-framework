<?php
class Kwf_Component_Abstract_ExtConfig_Form extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $ret = array(
            'form' => $this->_getStandardConfig('kwf.autoform')
        );
        return $ret;
    }
}
