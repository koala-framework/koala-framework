<?php
class Vps_Acl extends Zend_Acl
{
    public function __construct()
    {
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Vps_Acl_Role_Admin('admin', 'Administrator'));
        $this->addRole(new Zend_Acl_Role('cli'));

        $this->add(new Zend_Acl_Resource('index'));
        $this->add(new Zend_Acl_Resource('vps_user_menu'));
        $this->add(new Zend_Acl_Resource('vps_user_login'));
        $this->add(new Zend_Acl_Resource('vps_user_changeuser'));
        $this->add(new Zend_Acl_Resource('vps_error_error'));
        $this->add(new Zend_Acl_Resource('vps_user_about'));
        $this->add(new Zend_Acl_Resource('vps_welcome_index'));
        $this->add(new Zend_Acl_Resource('vps_welcome_content'));
        $this->add(new Zend_Acl_Resource('vps_debug'));
        $this->add(new Zend_Acl_Resource('vps_debug_sql'), 'vps_debug');
        $this->add(new Zend_Acl_Resource('vps_debug_assets'), 'vps_debug');
        $this->add(new Zend_Acl_Resource('vps_debug_activate'), 'vps_debug');
        $this->add(new Zend_Acl_Resource('vps_media_upload'));
        $this->add(new Zend_Acl_Resource('edit_role'));
        $this->add(new Vps_Acl_Resource_EditRole('edit_role_admin', 'admin'), 'edit_role');

        $this->add(new Vps_Acl_Resource_UserSelf('vps_user_self', '/vps/user/self'));

        $this->add(new Zend_Acl_Resource('vps_cli_help'));
        $this->add(new Zend_Acl_Resource('vps_cli_index'));
        $this->add(new Zend_Acl_Resource('vps_cli_trlparse'));
        $this->add(new Zend_Acl_Resource('vps_cli_hlpparse'));
        $this->allow('cli', 'vps_cli_help');
        $this->allow('cli', 'vps_cli_index');
        $this->allow('cli', 'vps_cli_trlparse');
        $this->allow('cli', 'vps_cli_hlpparse');

        $this->allow(null, 'index');
        $this->deny('guest', 'index');
        $this->allow(null, 'vps_user_menu');
        $this->allow(null, 'vps_user_login');
        $this->allow(null, 'vps_error_error');
        $this->allow(null, 'vps_user_about');
        $this->allow(null, 'vps_welcome_index');
        $this->allow(null, 'vps_welcome_content');
        $this->deny('guest', 'vps_welcome_index');
        $this->allow(null, 'vps_user_self');
        $this->deny('guest', 'vps_user_self');
        $this->allow('admin', 'vps_debug');
        $this->allow('admin', 'vps_media_upload');
        $this->allow('admin', 'edit_role');
    }

    public function isAllowedUser($user, $resource = null, $privilege = null)
    {
//     d('a');
        if (is_numeric($user)) {
            $table = Vps_Registry::get('userModel');
            $user = $table->find($user)->current();
        }
//         d('b');

        if (!$user) {
            return $this->isAllowed('guest', $resource, $privilege);
        }

        $userRoles      = array($user->role);
        $availableRoles = array($user->role);

        $additionalRolesExist = false;
        foreach ($this->getRoles() as $roleObj) {
            if ($roleObj instanceof Vps_Acl_Role_Additional
                && $roleObj->getParentRoleId() == $user->role
            ) {
                $availableRoles[] = $roleObj->getRoleId();
                $additionalRolesExist = true;
            }
        }

        if ($additionalRolesExist) {
            $table = new Vps_Model_User_AdditionalRoles();
            $rows = $table->fetchAll(array('user_id = ?' => $user->id));
            foreach ($rows as $r) {
                $userRoles[] = $r->additional_role;
            }
        }

        foreach ($userRoles as $r) {
            if (in_array($r, $availableRoles) && $this->isAllowed($r, $resource, $privilege)) {
                return true;
            }
        }

        return false;
    }

    public function getResources($parent = null)
    {
        $ret = array();
        $resourceParent = null;

        if (null !== $parent) {
            try {
                if ($parent instanceof Zend_Acl_Resource_Interface) {
                    $resourceParentId = $parent->getResourceId();
                } else {
                    $resourceParentId = $parent;
                }
                $resourceParent = $this->get($resourceParentId);
            } catch (Zend_Acl_Exception $e) {
                throw new Zend_Acl_Exception(trlVps("Parent Resource id {0} does not exist", '\''.$resourceParentId.'\''));
            }
        } else {
            $resourceParentId = null;
        }

        foreach ($this->_resources as $resource) {
            if ($resource['parent'] !== null) {
                $id = $resource['parent']->getResourceId();
            } else {
                $id = null;
            }
            if ($id === $resourceParentId) {
                $ret[] = $resource['instance'];
            }
        }
        return $ret;
    }

    public function getAllResources()
    {
        $ret = array();
        foreach ($this->_resources as $resource) {
            $ret[] = $resource['instance'];
        }
        return $ret;
    }

    protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = new Vps_Acl_Role_Registry();
        }
        return $this->_roleRegistry;
    }

    public function getRoles()
    {
        return $this->_getRoleRegistry()->getRoles();
    }
}
