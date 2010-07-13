<?php
class Vpc_Directories_Item_Directory_Admin extends Vpc_Admin
{
    /** entfernt, stattdessen editComponent setting in detail setzen **/
    //(final damit exception kommt)
    protected final function _getContentClass()
    { return null; }

    private function _getEditConfigs($componentClass, Vps_Component_Generator_Abstract $gen, $idTemplate, $componentIdSuffix)
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

    public function getExtConfig()
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
        foreach ($this->_getPluginAdmins() as $a) {
            $componentPlugins[] = $a->getPluginExtConfig();
        }
        $name = $this->_getSetting('componentName');
        if (strpos($name, '.') !== false) $name = substr(strrchr($name, '.'), 1);

        return array(
            'items' => array(
                'xtype'=>'vpc.directories.item.directory',
                'controllerUrl' => $this->getControllerUrl(),
                'title' => trlVps('Edit {0}', $name),
                'icon' => $this->_getSetting('componentIcon')->__toString(),
                'componentConfigs' => $edit['componentConfigs'],
                'contentEditComponents' => $edit['contentEditComponents'],
                'componentPlugins' => $componentPlugins
            )
        );
    }

    protected function _getPluginAdmins()
    {
        $lookForPluginClasses = $this->_getPluginParentComponents();
        $classes = array();
        foreach ($lookForPluginClasses as $c) {
            $classes = array_merge($classes, Vpc_Abstract::getChildComponentClasses($c));
        }
        $ret = array();
        foreach ($classes as $class) {
            $admin = Vpc_Admin::getInstance($class);
            if ($admin instanceof Vpc_Directories_Item_Directory_PluginAdminInterface) {
                $ret[] = $admin;
            }
        }
        return $ret;
    }

    protected function _getPluginParentComponents()
    {
        return array();
    }

    public function delete($componentId)
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        Vpc_Admin::getInstance($detail)->delete($componentId);
    }
}
