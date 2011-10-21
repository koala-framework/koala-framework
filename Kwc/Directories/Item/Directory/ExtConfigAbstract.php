<?php
abstract class Kwc_Directories_Item_Directory_ExtConfigAbstract extends Kwf_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $gen = Kwf_Component_Generator_Abstract::getInstance($this->_class, 'detail');
        $generators = Kwc_Abstract::getSetting($this->_class, 'generators');
        $idTemplate = null;
        if (isset($generators['detail']['dbIdShortcut'])) {
            $idTemplate = $generators['detail']['dbIdShortcut'].'{0}';
        }

        $componentPlugins = array();
        foreach ($this->_getAdmin()->getPluginAdmins() as $a) {
            $componentPlugins[] = $a->getPluginExtConfig();
        }

        $config = $this->_getStandardConfig('kwc.directories.item.directory');
        $detailClasses = Kwc_Abstract::getChildComponentClasses($this->_class, 'detail');
        $componentConfigs = array();
        $contentEditComponents = array();
        foreach ($detailClasses as $type => $detailClass) {
            $edit = Kwf_Component_Abstract_ExtConfig_Abstract::getEditConfigs($detailClass, $gen, $idTemplate, '');
            $componentConfigs = array_merge($componentConfigs, $edit['componentConfigs']);
            foreach ($edit['contentEditComponents'] as $ec) {
                $ec['component'] = $type;
                $contentEditComponents[] = $ec;
            }
        }

        $config['componentConfigs'] = $componentConfigs;
        $config['contentEditComponents'] = $contentEditComponents;
        $config['componentPlugins'] = $componentPlugins;
        $config['needsComponentPanel'] = true;
        $config['idSeparator'] = '_';
        return array(
            'items' => $config
        );
    }
}
