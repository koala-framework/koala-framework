<?php
class Vps_Acl_Resource_MenuUrl extends Vps_Acl_Resource_Abstract
{
    protected $_menuUrl;

    public function __construct($resourceId, $menuConfig = null, $menuUrl = null)
    {
        $this->_menuUrl = $menuUrl;
        parent::__construct($resourceId, $menuConfig);
    }

    public function setMenuUrl($menuUrl)
    {
        $this->_menuUrl = $menuUrl;
    }

    public function getMenuUrl()
    {
        return $this->_menuUrl;
    }
}
