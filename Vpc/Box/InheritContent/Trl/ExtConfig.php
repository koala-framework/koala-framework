<?php
class Vpc_Box_InheritContent_Trl_ExtConfig extends Vps_Component_Abstract_ExtConfig_Form
{
    protected function _getConfig()
    {
        if (!$this->_getSetting('hasVisible')) {
            return array();
        }
        return parent::_getConfig();
    }
}
