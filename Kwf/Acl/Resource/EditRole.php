<?php
/**
 * Resource ob ein Benutzer mit der Rolle in der Admin bearbeitet werden darf.
 */
class Vps_Acl_Resource_EditRole extends Zend_Acl_Resource
{
    protected $_roleId;

    public function __construct($resourceId, $roleId)
    {
        $this->setRoleId($roleId);
        parent::__construct($resourceId);
    }

    public function setRoleId($roleId)
    {
        $this->_roleId = $roleId;
    }

    public function getRoleId()
    {
        return $this->_roleId;
    }
}
