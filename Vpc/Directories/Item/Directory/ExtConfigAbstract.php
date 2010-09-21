<?php
abstract class Vpc_Directories_Item_Directory_ExtConfigAbstract extends Vps_Component_Abstract_ExtConfig_Abstract
{
    //TODO kopie von Vpc_Paragraphs_EditComponentsData
    protected final function _getEditConfigs($componentClass, Vps_Component_Generator_Abstract $gen, $idTemplate, $componentIdSuffix)
    {
        $ret = array(
            'componentConfigs' => array(),
            'contentEditComponents' => array(),
        );
        $cfg = Vpc_Admin::getInstance($componentClass)->getExtConfig();
        foreach ($cfg as $k=>$c) {
            $ret['componentConfigs'][$componentClass.'-'.$k] = $c;
            $ret['contentEditComponents'][] = array(
                'componentClass' => $componentClass,
                'type' => $k,
                'idTemplate' => $idTemplate,
                'componentIdSuffix' => $componentIdSuffix
            );
        }
        foreach ($gen->getGeneratorPlugins() as $plugin) {
            $cls = get_class($plugin);
            $cfg = Vpc_Admin::getInstance($cls)->getExtConfig();
            foreach ($cfg as $k=>$c) {
                $ret['componentConfigs'][$cls.'-'.$k] = $c;
                $ret['contentEditComponents'][] = array(
                    'componentClass' => $cls,
                    'type' => $k,
                    'idTemplate' => $idTemplate,
                    'componentIdSuffix' => $componentIdSuffix
                );
            }
        }
        if (Vpc_Abstract::hasSetting($componentClass, 'editComponents')) {
            $editComponents = Vpc_Abstract::getSetting($componentClass, 'editComponents');
            foreach ($editComponents as $c) {
                $childGen = Vps_Component_Generator_Abstract::getInstances($componentClass, array('componentKey'=>$c));
                $childGen = $childGen[0];
                $cls = Vpc_Abstract::getChildComponentClass($componentClass, null, $c);
                $edit = $this->_getEditConfigs($cls, $childGen,
                                               $idTemplate,
                                               $componentIdSuffix.$childGen->getIdSeparator().$c);
                $ret['componentConfigs'] = array_merge($ret['componentConfigs'], $edit['componentConfigs']);
                $ret['contentEditComponents'] = array_merge($ret['contentEditComponents'], $edit['contentEditComponents']);
            }
        }
        return $ret;
    }
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

        $config = $this->_getStandardConfig('vpc.directories.item.directory');
        $config['componentConfigs'] = $edit['componentConfigs'];
        $config['contentEditComponents'] = $edit['contentEditComponents'];
        $config['componentPlugins'] = $componentPlugins;
        $config['needsComponentPanel'] = true;
        return array(
            'items' => $config
        );
    }
}
