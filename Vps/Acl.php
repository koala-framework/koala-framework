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

        $this->add(new Zend_Acl_Resource('vps_spam_set'));

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
        $this->allow(null, 'vps_spam_set');
    }

    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        $ret = parent::isAllowed($role, $resource, $privilege);

        if (!$ret) {
            if (null !== $resource) {
                $resource = $this->get($resource);
            }

            if ($resource instanceof Vps_Acl_Resource_MenuDropdown) {
                foreach ($this->getResources($resource) as $r) {
                    if ($r instanceof Vps_Acl_Resource_MenuUrl
                        && parent::isAllowed($role, $r, $privilege)
                    ) {
                        $ret = true;
                        break;
                    }
                }
            }
        }

        return $ret;
    }

    public function isAllowedUser($user, $resource = null, $privilege = null)
    {
        if (is_numeric($user)) {
            $table = Vps_Registry::get('userModel');
            $user = $table->find($user)->current();
        }

        if (!$user) {
            return $this->isAllowed('guest', $resource, $privilege);
        }

        if ($this->isAllowed($user->role, $resource, $privilege)) {
            return true;
        }

        $additionalRoles = $this->_getAdditionalRolesByRole($user->role);
        if ($additionalRoles) {
            foreach ($user->getAdditionalRoles() as $r) {
                if (in_array($r, $additionalRoles) && $this->isAllowed($r, $resource, $privilege)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function _getAdditionalRolesByRole($role)
    {
        $ret = array();
        foreach ($this->getRoles() as $r) {
            if ($r instanceof Vps_Acl_Role_Additional
                && $r->getParentRoleId() == $role
            ) {
                $ret[] = $r->getRoleId();
            }
        }
        return $ret;
    }

    public function getAllowedEditResourceRoleIdsByRole($role)
    {
        $ret = array();
        foreach ($this->_getAllowedEditResourcesByRole($role) as $res) {
            $ret[] = $res->getRoleId();
        }
        return $ret;
    }

    private function _getAllowedEditResourcesByRole($role)
    {
        $ret = array();
        foreach ($this->getAllResources() as $r) {
            if ($r instanceof Vps_Acl_Resource_EditRole
                && $this->isAllowed($role, $r, 'view')
            ) {
                $ret[] = $r;
            }
        }
        return $ret;
    }

    public function getAllowedEditRolesByRole($role)
    {
        $ret = array();
        $editResourceRoleIds = $this->getAllowedEditResourceRoleIdsByRole($role);
        foreach ($this->getRoles() as $role) {
            if ($role instanceof Vps_Acl_Role && !($role instanceof Vps_Acl_Role_Additional)
                && in_array($role->getRoleId(), $editResourceRoleIds)
            ) {
                $ret[] = $role;
            }
        }
        return $ret;
    }

    public function getAdditionalRoles()
    {
        $ret = array();
        foreach ($this->getRoles() as $role) {
            if ($role instanceof Vps_Acl_Role_Additional) {
                $ret[] = $role;
            }
        }
        return $ret;
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
