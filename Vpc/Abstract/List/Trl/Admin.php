<?php
class Vpc_Abstract_List_Trl_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->_class, 'child');
        $childConfig = array_values(Vpc_Admin::getInstance($class)->getExtConfig());
        if (count($childConfig) > 1) {
            //wenn das mal benötigt wird möglicherwesie mit tabs
            throw new Vps_Exception("Vpc_Abstract_List can only have childs with one Controller '$class'");
        } else if (!count($childConfig)) {
            throw new Vps_Exception("Vpc_Abstract_List must have child with at least one ExtConfig");
        }

        return array(
            'list' => array(
                'xtype'=>'vpc.list',
                'controllerUrl' => $this->getControllerUrl(),
                'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
                'icon' => $this->_getSetting('componentIcon')->__toString(),
                'childConfig'=>$childConfig[0]
            )
        );
    }
}
