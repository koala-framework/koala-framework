<?php
class Vps_Acl_Role extends Zend_Acl_Role
{
    protected $_roleName;

    public function __construct($roleId, $roleName = null)
    {
        $this->_roleName = $roleName;
        parent::__construct($roleId);
    }

    public function setRoleName($roleName)
    {
        $this->_roleName = $roleName;
    }

    public function getRoleName()
    {
        return $this->_roleName;
    }
}
