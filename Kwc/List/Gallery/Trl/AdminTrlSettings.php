<?php
/**
 * Kann im Web verwendet werden, wenn Einstellungen in der Settings-Form
 * übersetzt werden müssen
 */
class Kwc_List_Gallery_Trl_AdminTrlSettings extends Kwc_Abstract_List_Trl_Admin
{
    public function getExtConfig()
    {
        $listConfig = parent::getExtConfig();

        $config = array(
            'title' => trlKwf('Edit {0}', $this->_getSetting('componentName')),
            'icon' => $this->_getSetting('componentIcon')->__toString(),
            'activeTab' => 0,
            'xtype' => 'kwf.tabpanel',
            'tabs' => array()
        );

        $config['tabs']['settings'] = array(
            'xtype' => 'kwf.autoform',
            'controllerUrl' => $this->getControllerUrl('Settings'),
            'title' => trlKwf('Settings')
        );
        $config['tabs']['images'] = $listConfig['list'];

        return array('tabs' => $config);
    }
}
