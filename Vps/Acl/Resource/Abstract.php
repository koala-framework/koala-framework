<?php
abstract class Vps_Acl_Resource_Abstract extends Zend_Acl_Resource
{
    protected $_menuConfig;

    public function __construct($resourceId, $menuConfig = null)
    {
        $this->_menuConfig = $menuConfig;
        parent::__construct($resourceId);
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
