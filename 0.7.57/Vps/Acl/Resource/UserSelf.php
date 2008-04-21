<?php
class Vps_Acl_Resource_UserSelf extends Zend_Acl_Resource
{
    protected $_controllerUrl;

    public function __construct($resourceId, $controllerUrl)
    {
        $this->_controllerUrl = $controllerUrl;
        parent::__construct($resourceId);
    }

    public function setControllerUrl($controllerUrl)
    {
        $this->_controllerUrl = $controllerUrl;
    }
    public function getControllerUrl()
    {
        return $this->_controllerUrl;
    }
}
