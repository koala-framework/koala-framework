<?php
class Vpc_Box_InheritContent_Trl_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        if ($this->_getSetting('hasVisible')) {
            return array(
                'form' => array(
                    'xtype' => 'vps.autoform',
                    'controllerUrl' => $this->getControllerUrl(),
                    'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
                    'icon' => $this->_getSetting('componentIcon')->__toString()
                )
            );
        }
        return array();
    }
}