<?php
class Vps_Acl_Resource_MenuCommand extends Vps_Acl_Resource_Abstract
{
    protected $_menuCommandClass;
    protected $_menuConfig;

    public function __construct($resourceId, $menuText = null, $class = null, $config = null)
    {
        $this->_menuCommandClass = $class;
        $this->_menuConfig = $config;
        parent::__construct($resourceId, $menuText);
    }

    public function setMenuCommandClass($menuClass)
    {
        $this->_menuCommandClass = $menuClass;
    }

    public function getMenuCommandClass()
    {
        return $this->_menuCommandClass;
    }

    public function setMenuConfig($menuConfig)
    {
        $this->_menuConfig = $menuConfig;
    }

    public function getMenuConfig()
    {
        return $this->_menuConfig;
    }
}
