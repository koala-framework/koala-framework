<?php
class Vpc_Directories_Item_Directory_EdtConfigTabs extends Vpc_Directories_Item_Directory_ExtConfigEditButtons
{
    protected function _getConfig()
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        $gen = Vps_Component_Generator_Abstract::getInstance($this->_class, 'detail');
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        if (isset($generators['detail']['dbIdShortcut'])) {
            $idTemplate = $generators['detail']['dbIdShortcut'].'{0}';
        } else {
            $idTemplate = '{componentId}'.$gen->getIdSeparator().'{0}';
        }
        $edit = $this->_getEditConfigs($detail, $gen, $idTemplate, '');

        $componentPlugins = array();
        foreach ($this->_getAdmin()->getPluginAdmins() as $a) {
            $componentPlugins[] = $a->getPluginExtConfig();
        }

        $config = $this->_getStandardConfig('vpc.directories.item.directory.tabs');
        $config['componentConfigs'] = $edit['componentConfigs'];
        $config['contentEditComponents'] = $edit['contentEditComponents'];
        $config['componentPlugins'] = $componentPlugins;
        $config['detailsControllerUrl'] = $this->getControllerUrl('Form');
        return array(
            'items' => $config
        );
    }
}