<?php
abstract class Kwc_Directories_Item_Directory_ExtConfigAbstract extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $detail = Kwc_Abstract::getChildComponentClass($this->_class, 'detail');
        $gen = Kwf_Component_Generator_Abstract::getInstance($this->_class, 'detail');
        $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
        $idTemplate = null;
        if (isset($generators['detail']['dbIdShortcut'])) {
            $idTemplate = $generators['detail']['dbIdShortcut'].'{0}';
        }
        $edit = Kwf_Component_Abstract_ExtConfig_Abstract::getEditConfigs($detail, $gen, $idTemplate);

        $componentPlugins = array();
        foreach ($this->_getAdmin()->getPluginAdmins() as $a) {
            $componentPlugins[] = $a->getPluginExtConfig();
        }

        $config = $this->_getStandardConfig('kwc.directories.item.directory');
        $config['componentConfigs'] = $edit['componentConfigs'];
        $config['contentEditComponents'] = $edit['contentEditComponents'];
        $config['componentPlugins'] = $componentPlugins;
        $config['needsComponentPanel'] = true;
        return array(
            'items' => $config
        );
    }
}
