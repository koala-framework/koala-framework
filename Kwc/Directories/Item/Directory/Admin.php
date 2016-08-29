<?php
class Kwc_Directories_Item_Directory_Admin extends Kwc_Admin
{
    /** entfernt, stattdessen editComponent setting in detail setzen **/
    //(final damit exception kommt)
    protected final function _getContentClass()
    { return null; }

    protected function _getPluginAdmins()
    {
        $lookForPluginClasses = $this->_getPluginParentComponents();
        $classes = array();
        foreach ($lookForPluginClasses as $c) {
            $classes = array_merge($classes, Kwc_Abstract::getChildComponentClasses($c));
        }
        $ret = array();
        foreach ($classes as $class) {
            $admin = Kwc_Admin::getInstance($class);
            if ($admin instanceof Kwc_Directories_Item_Directory_PluginAdminInterface) {
                $ret[] = $admin;
            }
        }
        return $ret;
    }

    protected function _getPluginParentComponents()
    {
        return array($this->_class);
    }

    public final function getPluginAdmins()
    {
        return $this->_getPluginAdmins();
    }
}
