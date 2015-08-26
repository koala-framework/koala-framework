<?php
class Kwc_Box_InheritContent_Trl_ExtConfig extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        if (!$this->_getSetting('hasVisible')) {
            return array();
        } else {
            $g = Kwc_Abstract::getSetting($this->_class, 'generators');
            $childConfig = Kwc_Admin::getInstance($g['child']['component']['child'])->getExtConfig();
            $ret = array(
                'form' => $this->_getStandardConfig('kwf.autoform', 'Index',
                        $childConfig['form']['title'], $childConfig['form']['icon'])
            );
        }
        return $ret;
    }
}
