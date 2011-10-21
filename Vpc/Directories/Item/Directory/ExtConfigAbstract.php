<?php
abstract class Vpc_Directories_Item_Directory_ExtConfigAbstract extends Vps_Component_Abstract_ExtConfig_Abstract
{
    protected function _getConfig()
    {
        $gen = Vps_Component_Generator_Abstract::getInstance($this->_class, 'detail');
        $generators = Vpc_Abstract::getSetting($this->_class, 'generators');
        if (isset($generators['detail']['dbIdShortcut'])) {
            $idTemplate = $generators['detail']['dbIdShortcut'].'{0}';
        } else {
            $idTemplate = '{componentId}'.$gen->getIdSeparator().'{0}';
        }

        $componentPlugins = array();
        foreach ($this->_getAdmin()->getPluginAdmins() as $a) {
            $componentPlugins[] = $a->getPluginExtConfig();
        }

        $config = $this->_getStandardConfig('vpc.directories.item.directory');
        $detailClasses = Vpc_Abstract::getChildComponentClasses($this->_class, 'detail');
        $componentConfigs = array();
        $contentEditComponents = array();
        foreach ($detailClasses as $type => $detailClass) {
            $edit = Vps_Component_Abstract_ExtConfig_Abstract::getEditConfigs($detailClass, $gen, $idTemplate, '');
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
