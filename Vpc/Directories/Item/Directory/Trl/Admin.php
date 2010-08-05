<?php
class Vpc_Directories_Item_Directory_Trl_Admin extends Vpc_Directories_Item_Directory_Admin
{
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

    public final function getPluginAdmins()
    {
        return $this->_getPluginAdmins();
    }
}
