<?php
/**
 * List mit child daneben; list ist immer sichtbar
 */
class Vpc_Abstract_List_ExtConfigList extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->_class, 'child');
        $childConfig = array_values(Vpc_Admin::getInstance($class)->getExtConfig());
        if (count($childConfig) > 1) {
            //wenn das mal benötigt wird möglicherwesie mit tabs
            throw new Vps_Exception("Vpc_Abstract_List can only have childs with one Controller '$class'");
        } else if (!count($childConfig)) {
            throw new Vps_Exception("Vpc_Abstract_List must have child with at least one ExtConfig");
        }

        $config = $this->_getStandardConfig('vpc.list.list');
        $config['childConfig'] = $childConfig[0];
        return array(
            'list' => $config
        );
    }
}