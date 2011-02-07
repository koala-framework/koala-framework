<?php
class Vpc_List_Gallery_Admin extends Vpc_Abstract_List_Admin
{
    public function getExtConfig()
    {
        $listConfig = parent::getExtConfig();

        $config = array(
            'title' => trlVps('Edit {0}', $this->_getSetting('componentName')),
            'icon' => $this->_getSetting('componentIcon')->__toString(),
            'activeTab' => 0,
            'xtype' => 'vps.tabpanel',
            'tabs' => array()
        );

        $config['tabs']['settings'] = array(
            'xtype' => 'vps.autoform',
            'controllerUrl' => $this->getControllerUrl('Settings'),
            'title' => trlVps('Settings')
        );
        $config['tabs']['images'] = $listConfig['list'];

        return array('tabs' => $config);
    }
}
