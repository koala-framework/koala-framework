<?php
class Vpc_ListChildPages_Teaser_Admin extends Vpc_Admin
{/*
    public function getExtConfig()
    {
        $ret = array();

        $url = Vpc_Admin::getInstance($this->_class)->getControllerUrl();
        $icon = new Vps_Asset('wrench');
        $ret['table'] = array(
            'xtype' => 'vps.autogrid',
            'controllerUrl' => $url,
            'title' => trlVps('Teaser'),
            'icon' => $icon->__toString()
        );

        return $ret;
    }*/
    public function getExtConfig()
    {
        $class = Vpc_Abstract::getChildComponentClass($this->_class, 'child');
        $childConfig = array_values(Vpc_Admin::getInstance($class)->getExtConfig());

        return array(
            'list' => array(
                'xtype' => 'vpc.listchildpages',
                'controllerUrl' => $this->getControllerUrl(),
                'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
                'icon' => $this->_getSetting('componentIcon')->__toString(),
                'childConfig'=>$childConfig[0]
            )
        );
    }
}
