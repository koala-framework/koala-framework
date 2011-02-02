<?php
class Vpc_List_ChildPages_Teaser_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $config = $this->_getStandardConfig('vpc.list');

        $class = Vpc_Abstract::getChildComponentClass($this->_class, 'child');
        $childConfig = array_values(Vpc_Admin::getInstance($class)->getExtConfig());
        $config['childConfig'] = $childConfig[0];

        return array(
            'list' => $config
        );
    }
}
