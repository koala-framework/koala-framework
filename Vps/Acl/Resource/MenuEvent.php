<?php
class Vps_Acl_Resource_MenuEvent extends Vps_Acl_Resource_MenuUrl
{
    protected $_menuConfig;

    public function __construct($resourceId, $menuText = null, $menuConfig = null)
    {
        $this->_menuConfig = $menuConfig;
        parent::__construct($resourceId, $menuText);
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
