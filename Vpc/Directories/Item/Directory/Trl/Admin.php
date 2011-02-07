<?php
class Vpc_Directories_Item_Directory_Trl_Admin extends Vpc_Admin
{
    protected function _getContentClass()
    {
        return null;
    }

    public function getExtConfig()
    {
        $componentConfigs = array();
        $contentEditComponents = array();

        $contentClass = $this->_getContentClass();
        $cfgKeys = array();
        if ($contentClass) {
            $cfg = Vpc_Admin::getInstance($contentClass)->getExtConfig();
            foreach ($cfg as $k=>$c) {
                $componentConfigs[$contentClass.'-'.$k] = $c;
                $contentEditComponents[] = array(
                    'componentClass' => $contentClass,
                    'type' => $k
                );
            }
            $cfgKeys = array_keys($cfg);
        }

        $componentPlugins = array();
        foreach ($this->_getPluginAdmins() as $a) {
            $componentPlugins[] = $a->getPluginExtConfig();
        }
        $name = $this->_getSetting('componentName');
        if (strpos($name, '.') !== false) $name = substr(strrchr($name, '.'), 0, 1);

        $ret = array(
            'items' => array(
                'xtype'=>'vpc.directories.item.directory',
                'controllerUrl' => $this->getControllerUrl(),
                'title' => trlVps('Edit {0}', $name),
                'icon' => $this->_getSetting('componentIcon')->__toString(),
                'contentClass' => $contentClass,
                'contentType' => $cfgKeys ? $cfgKeys[0] : null,
                'componentConfigs' => $componentConfigs,
                'contentEditComponents' => $contentEditComponents,
                'componentPlugins' => $componentPlugins
            )
        );
        return $ret;
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
}
