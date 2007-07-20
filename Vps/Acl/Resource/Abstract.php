<?php
abstract class Vps_Acl_Resource_Abstract extends Zend_Acl_Resource
{
    protected $_menuText;

    public function __construct($resourceId, $menuText = null)
    {
        $this->_menuText = $menuText;
        parent::__construct($resourceId);
    }

    public function setMenuText($menuText)
    {
        $this->_menuText = $menuText;
    }

    public function getMenuText()
    {
        return $this->_menuText;
    }
}
