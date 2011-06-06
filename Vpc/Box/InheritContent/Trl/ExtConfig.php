<?php
class Vpc_Box_InheritContent_Trl_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        if (!$this->_getSetting('hasVisible')) {
            return array();
        } else {
            //in diesem fall gibt es genau eine config vom child namens form
            //hasVisible wird in Component::getSettings nach diesem Kriterium gesetzt
            $g = Vpc_Abstract::getSetting($this->_class, 'generators');
            $childConfig = Vpc_Admin::getInstance($g['child']['component']['child'])->getExtConfig();
            $ret = array(
                'form' => $this->_getStandardConfig('vps.autoform', 'Index',
                        $childConfig['form']['title'], $childConfig['form']['icon'])
            );
        }
        return $ret;
    }
}
