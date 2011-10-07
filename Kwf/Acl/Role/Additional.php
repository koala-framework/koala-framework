<?php
class Vps_Acl_Role_Additional extends Vps_Acl_Role
{
    protected $_parentRoleId;

    public function __construct($roleId, $roleName, $parentRoleId)
    {
        $this->setParentRoleId($parentRoleId);
        parent::__construct($roleId, $roleName);
    }

    public function setParentRoleId($parentRoleId)
    {
        $this->_parentRoleId = $parentRoleId;
    }

    public function getParentRoleId()
    {
        return $this->_parentRoleId;
    }
}
