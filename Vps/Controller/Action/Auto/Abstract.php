<?php
abstract class Vps_Controller_Action_Auto_Abstract extends Vps_Controller_Action
{
    protected $_buttons = array();
    protected $_permissions;
    private $_helpText;

    public function init()
    {
        parent::init();

        if (!isset($this->_permissions)) {
            $this->_permissions = $this->_buttons;
        }

        $btns = array();
        foreach ($this->_buttons as $k=>$i) {
            if (is_int($k)) {
                $btns[$i] = true;
            } else {
                $btns[$k] = $i;
            }
        }
        $this->_buttons = $btns;

        $perms = array();
        foreach ($this->_permissions as $k=>$i) {
            if (is_int($k)) {
                $perms[$i] = true;
            } else {
                $perms[$k] = $i;
            }
        }
        $this->_permissions = $perms;

        //buttons/permissions abhängig von privileges in acl ausblenden/löschen
        $acl = $this->_getAcl();
        $authData = $this->_getAuthData();
        $resource = $this->getRequest()->getResourceName();

        foreach ($this->_buttons as $k=>$i) {
            if (!$acl->isAllowedUser($authData, $resource, $k)) {
                unset($this->_buttons[$k]);
            }
        }
        foreach ($this->_permissions as $k=>$i) {
            if (!$acl->isAllowedUser($authData, $resource, $k)) {
                unset($this->_permissions[$k]);
            }
        }
    }

    public final function setHelpText($helpText)
    {
        $this->_helpText = $helpText;
    }

    public final function getHelpText()
    {
        return $this->_helpText;
    }
}