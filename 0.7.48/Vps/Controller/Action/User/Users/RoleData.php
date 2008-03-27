<?php
class Vps_Controller_Action_User_Users_RoleData extends Vps_Auto_Data_Abstract
{
    private $_roles;

    public function __construct()
    {
        $acl = Zend_Registry::get('acl');
        $this->_roles = array();
        foreach($acl->getRoles() as $role) {
            if($role instanceof Vps_Acl_Role) {
                $this->_roles[$role->getRoleId()] = $role->getRoleName();
            }
        }
    }

    public function load($row)
    {
        if (isset($this->_roles[$row->role])) {
            return $this->_roles[$row->role];
        } else {
            return $row->role;
        }
    }
}
