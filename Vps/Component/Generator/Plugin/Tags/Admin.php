<?php
class Vps_Component_Generator_Plugin_Tags_Admin extends Vps_Component_Abstract_Admin
{
    public function getExtConfig()
    {
        $ret = array();
        $ret['tags'] = array(
            'xtype' => 'vps.assigngrid',
            'gridAssignedControllerUrl' => $this->getControllerUrl(),
            'gridDataControllerUrl' => $this->getControllerUrl('Tags'),
            'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
            'icon' => $this->_getSetting('componentIcon')->__toString()
        );
        return $ret;
    }
}
