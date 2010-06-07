<?php
class Vpc_Menu_Abstract_Admin extends Vpc_Admin
{
    public function getExtConfig()
    {
        $ret = array(
            'form' => array(
                'xtype' => 'vpc.menu.panel',
                'controllerUrl' => $this->getControllerUrl(),
                'formControllerUrl' => $this->getControllerUrl('Form'),
                'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
                'icon' => $this->_getSetting('componentIcon')->__toString()
            )
        );
        return $ret;
    }
}
