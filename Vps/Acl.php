<?php
class Vps_Acl extends Zend_Acl
{
    public function __construct()
    {
        $this->addRole(new Zend_Acl_Role('guest'));

        $this->add(new Zend_Acl_Resource('index'));
        $this->add(new Zend_Acl_Resource('vps_user_menu'));
        $this->add(new Zend_Acl_Resource('vps_user_login'));
        $this->add(new Zend_Acl_Resource('vps_user_loginmedia'));
        $this->add(new Zend_Acl_Resource('vps_error_error'));
        $this->add(new Zend_Acl_Resource('vps_user_about'));
        $this->add(new Zend_Acl_Resource('vps_welcome_index'));
        $this->add(new Zend_Acl_Resource('vps_welcome_content'));
        $this->add(new Zend_Acl_Resource('vps_welcome_media'));
        $this->add(new Zend_Acl_Resource('media'));

        $this->add(new Vps_Acl_Resource_UserSelf('vps_user_self', '/vps/user/self'));


        $this->allow(null, 'index');
        $this->deny('guest', 'index');
        $this->allow(null, 'vps_user_menu');
        $this->allow(null, 'vps_user_login');
        $this->allow(null, 'vps_user_loginmedia');
        $this->allow(null, 'vps_error_error');
        $this->allow(null, 'vps_user_about');
        $this->allow(null, 'vps_welcome_index');
        $this->allow(null, 'vps_welcome_content');
        $this->deny('guest', 'vps_welcome_index');
        $this->allow(null, 'vps_welcome_media');
        $this->deny('guest', 'vps_welcome_media');
        $this->allow(null, 'vps_user_self');
        $this->deny('guest', 'vps_user_self');
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
                throw new Zend_Acl_Exception("Parent Resource id '$resourceParentId' does not exist");
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
