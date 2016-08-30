<?php
class Kwf_Controller_Action_User_Users_RoleData extends Kwf_Data_Abstract
{
    private $_roles;

    public function __construct()
    {
        $acl = Zend_Registry::get('acl');
        $this->_roles = array();
        foreach($acl->getRoles() as $role) {
            if($role instanceof Kwf_Acl_Role) {
                $roleName = Kwf_Trl::getInstance()->trlStaticExecute($role->getRoleName());
                $this->_roles[$role->getRoleId()] = $roleName;
            }
        }
    }

    public function load($row, array $info)
    {
        if (isset($this->_roles[$row->role])) {
            return $this->_roles[$row->role];
        } else {
            return $row->role;
        }
    }
}
