<?php
class Vpc_Directories_Item_Directory_Admin extends Vpc_Admin
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

    public function delete($componentId)
    {
        $detail = Vpc_Abstract::getChildComponentClass($this->_class, 'detail');
        Vpc_Admin::getInstance($detail)->delete($componentId);
    }
}
