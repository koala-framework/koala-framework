<?php
class Vpc_List_ChildPages_Teaser_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->_class, 'child');
        $childConfig = array_values(Vpc_Admin::getInstance($class)->getExtConfig());

        return array(
            'list' => array(
                'xtype' => 'vpc.list',
                'controllerUrl' => $this->getControllerUrl(),
                'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
                'icon' => $this->_getSetting('componentIcon')->__toString(),
                'childConfig'=>$childConfig[0]
            )
        );
    }
}
