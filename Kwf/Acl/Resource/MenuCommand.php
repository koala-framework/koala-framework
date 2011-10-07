<?php
class Vps_Acl_Resource_MenuCommand extends Vps_Acl_Resource_Abstract
{
    protected $_menuCommandClass;
    protected $_menuCommandConfig;

    public function __construct($resourceId, $menuConfig = null, $class = null, $menuCommandConfig = null)
    {
        $this->_menuCommandClass = $class;
        $this->_menuCommandConfig = $menuCommandConfig;
        parent::__construct($resourceId, $menuConfig);
    }

    public function setMenuCommandClass($menuClass)
    {
        $this->_menuCommandClass = $menuClass;
    }

    public function getMenuCommandClass()
    {
        return $this->_menuCommandClass;
    }

    public function setMenuCommandConfig($menuCommandConfig)
    {
        $this->_menuCommandConfig = $menuCommandConfig;
    }

    public function getMenuCommandConfig()
    {
        return $this->_menuCommandConfig;
    }
}
