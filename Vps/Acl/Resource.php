<?php
class Vps_Acl_Resource extends Zend_Acl_Resource
{
    protected $_menuText;
    protected $_menuUrl;
    protected $_asEvent;

    public function __construct($resourceId, $menuText = null, $menuUrl = null, $asEvent = false)
    {
        $this->_menuText = $menuText;
        $this->_menuUrl = $menuUrl;
        $this->_asEvent = $asEvent;
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

    public function setMenuUrl($menuUrl)
    {
        $this->_menuUrl = $menuUrl;
    }

    public function getMenuUrl()
    {
        return $this->_menuUrl;
    }
    
    public function asEvent()
    {
        return $this->_asEvent;
    }
}
