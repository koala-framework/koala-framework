<?php
class Vps_Acl extends Zend_Acl
{
    protected $_componentAclClass = 'Vps_Component_Acl';

    /**
     * @var Vps_Component_Acl
     */
    protected $_componentAcl;
    protected $_vpcResourcesLoaded = false;

    public function __construct()
    {
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Vps_Acl_Role_Admin('admin', 'Administrator'));
        $this->addRole(new Zend_Acl_Role('cli'));

        $this->add(new Zend_Acl_Resource('default_index'));
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
        $this->add(new Zend_Acl_Resource('vps_debug_session-restart'), 'vps_debug');
        $this->add(new Zend_Acl_Resource('vps_debug_php-info'), 'vps_debug');
        $this->add(new Zend_Acl_Resource('vps_debug_apc'), 'vps_debug');
        $this->add(new Zend_Acl_Resource('vps_debug_assets-dependencies'), 'vps_debug');
        $this->add(new Zend_Acl_Resource('vps_media_upload'));
        $this->add(new Zend_Acl_Resource('vps_util_apc'));
        $this->add(new Zend_Acl_Resource('vps_util_render'));
        $this->add(new Zend_Acl_Resource('vps_test'));
        $this->add(new Zend_Acl_Resource('edit_role'));
        $this->add(new Vps_Acl_Resource_EditRole('edit_role_admin', 'admin'), 'edit_role');

        $this->add(new Vps_Acl_Resource_UserSelf('vps_user_self', '/vps/user/self'));

        $this->add(new Zend_Acl_Resource('vps_spam_set'));

        $this->add(new Zend_Acl_Resource('vps_cli'));
        $this->add(new Zend_Acl_Resource('vps_cli_help'));
        $this->add(new Zend_Acl_Resource('vps_cli_index'));
        $this->add(new Zend_Acl_Resource('vps_cli_trlparse'));
        $this->add(new Zend_Acl_Resource('vps_cli_hlpparse'));
        $this->add(new Zend_Acl_Resource('vps_test_connectionerror'));
        $this->allow('cli', 'vps_cli');
        $this->allow('cli', 'vps_cli_help');
        $this->allow('cli', 'vps_cli_index');
        $this->allow('cli', 'vps_cli_trlparse');
        $this->allow('cli', 'vps_cli_hlpparse');

        $this->allow(null, 'default_index');
        $this->allow(null, 'vps_test_connectionerror');
        $this->deny('guest', 'default_index');
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
        $this->allow(null, 'vps_media_upload');
        $this->allow(null, 'vps_util_apc');
        $this->allow(null, 'vps_util_render');
        $this->allow('admin', 'edit_role');
        $this->allow(null, 'vps_spam_set');
        $this->allow(null, 'vps_debug_session-restart');
        $this->allow(null, 'vps_test');
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
            $userModel = Vps_Registry::get('userModel');
            $user = $userModel->getRow($userModel->select()->whereEquals('id', $user));
        }

        if (!$user) {
            return $this->isAllowed('guest', $resource, $privilege);
        }
        $ret = $this->isAllowed($user->role, $resource, $privilege);
        if (!$ret) {
            $additionalRoles = $this->_getAdditionalRolesByRole($user->role);
            if ($additionalRoles) {
                foreach ($user->getAdditionalRoles() as $r) {
                    if (in_array($r, $additionalRoles) && $this->isAllowed($r, $resource, $privilege)) {
                        return true;
                    }
                }
            }
        }
        return $ret;
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
        $this->loadVpcResources();
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

    /**
     * Lädt Resourcen die von Komponenten kommen.
     * Muss extra aufgerufen werden wenn diese Resourcen benötigt werden, aus
     * performance gründen
     */
    public function loadVpcResources()
    {
        if ($this->_vpcResourcesLoaded) return;
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            if (Vpc_Abstract::getFlag($c, 'hasResources')) {
                Vpc_Admin::getInstance($c)->addResources($this);
            }
        }
        $this->_vpcResourcesLoaded = true;
    }

    public function getMenuConfig($user)
    {
        $this->loadVpcResources();
        return $this->_processResources($user, $this->getResources());
    }

    private function _processResources($user, $resources)
    {
        $menus = array();
        foreach ($resources as $resource) {
            if ($resource instanceof Vps_Acl_Resource_Component_Interface) {
                if (!$this->getComponentAcl()->isAllowed($user, $resource->getComponent())) continue;
            } else {
                if (!$this->isAllowedUser($user, $resource, 'view')) continue;
            }
            if ($resource instanceof Vps_Acl_Resource_ComponentClass_Interface) {
                if (!$this->getComponentAcl()->isAllowed($user, $resource->getComponentClass())) continue;
            } else {
                if (!$this->isAllowedUser($user, $resource, 'view')) continue;
            }
            if (!$resource instanceof Vps_Acl_Resource_Abstract) {
                //nur Vps-Resourcen im Menü anzeigen
                $menus = array_merge($menus, $this->_processResources($user, $this->getResources($resource)));
                continue;
            }
            $menu = array();
            $menu['menuConfig'] = $resource->getMenuConfig();
            if (is_string($menu['menuConfig'])) {
                $menu['menuConfig'] = array('text' => $menu['menuConfig']);
            }

            if (isset($menu['menuConfig']['icon'])) {
                if (is_string($menu['menuConfig']['icon'])) {
                    $menu['menuConfig']['icon'] = new Vps_Asset($menu['menuConfig']['icon']);
                }
                $menu['menuConfig']['icon'] = $menu['menuConfig']['icon']->__toString();
            }

            if ($resource instanceof Vps_Acl_Resource_MenuDropdown) {
                $menu['type'] = 'dropdown';
                $menu['children'] = $this->_processResources($user, $this->getResources($resource));
                if (!$menu['children']) {
                    //wenn keine children dropdown ignorieren
                    continue;
                }
            } else if ($resource instanceof Vps_Acl_Resource_MenuEvent) {
                $menu['type'] = 'event';
                $menu['eventConfig'] = $resource->getMenuEventConfig();
            } else if ($resource instanceof Vps_Acl_Resource_MenuUrl) {
                $menu['type'] = 'url';
                $menu['url'] = $resource->getMenuUrl();
            } else if ($resource instanceof Vps_Acl_Resource_MenuCommandDialog) {
                $menu['type'] = 'commandDialog';
                $menu['commandClass'] = $resource->getMenuCommandClass();
                $menu['commandConfig'] = $resource->getMenuCommandConfig();
            } else if ($resource instanceof Vps_Acl_Resource_MenuCommand) {
                $menu['type'] = 'command';
                $menu['commandClass'] = $resource->getMenuCommandClass();
                $menu['commandConfig'] = $resource->getMenuCommandConfig();
            } else if ($resource instanceof Vps_Acl_Resource_MenuSeparator) {
                $menu['type'] = 'separator';
            } else {
                $menu = $menu['menuConfig'];
            }
            $menus[] = $menu;
        }
        return $menus;
    }

    public function getComponentAcl()
    {
        if (!Vps_Component_Data_Root::getComponentClass()) return null;
        if (!isset($this->_componentAcl)) {
            $this->_componentAcl = new $this->_componentAclClass($this->_getRoleRegistry());
        }
        return $this->_componentAcl;
    }

    public function setComponentAcl(Vps_Component_Acl $componentAcl)
    {
        $this->_componentAcl = $componentAcl;
    }

    public function setComponentAclClass($class)
    {
        if (isset($this->_componentAcl)) {
            throw new Vps_Exception("Can't modify componentAclClass when getComponentAcl was called already");
        }
        $this->_componentAclClass = $class;
    }

    public function isAllowedComponent($class, $authData)
    {
        $allowed = false;
        foreach ($this->getAllResources() as $r) {
            if ($r instanceof Vps_Acl_Resource_ComponentClass_Interface) {
                if ($class == $r->getComponentClass()) {
                    $allowed = $this->getComponentAcl()->isAllowed($authData, $class);
                    break;
                }
            }
        }
        return $allowed;
    }

    public function isAllowedComponentById($componentId, $class, $authData)
    {
        $components = Vps_Component_Data_Root::getInstance()
            ->getComponentsByDbId($componentId, array('ignoreVisible'=>true));
        if (!$components) {
            throw new Vps_Exception("Can't find component to check permissions");
        }

        foreach ($components as $component) {
            // Basisprüfung
            $allowCheck = ($component->componentClass == $class);

            // Checken, ob übergebene componentClass auf der aktuellen Page vorkommen kann
            if (!$allowCheck) {
                $c = $component->parent;
                $stopComponent = $component->getPage();
                if (!is_null($stopComponent)) $stopComponent = $stopComponent->parent;
                while (!$allowCheck && $c &&
                    (!$stopComponent || $c->componentId != $stopComponent->componentId)
                ) {
                    $allowedComponentClasses = Vpc_Abstract::getChildComponentClasses(
                        $c->componentClass, array('page' => false)
                    );
                    $allowCheck = in_array($class, $allowedComponentClasses);
                    $c = $c->parent;
                }
            }

            // SharedDataClass braucht Sonderbehandlung, weil class die Komponente ist
            // und componentId aber auf die Shared-Komponente zeigt
            if (!$allowCheck) {
                $sharedDataClass = Vpc_Abstract::getFlag($class, 'sharedDataClass');
                $allowCheck = ($component->componentClass == $sharedDataClass);
            }

            //generator plugins erlauben
            if (!$allowCheck && $component->componentId != 'root') { //root hat keinen generator
                foreach ($component->generator->getGeneratorPlugins() as $p) {
                    if ($class == get_class($p)) {
                        $allowCheck = true;
                        break;
                    }
                }
            }

            // Nötig für news-link in link-komponente die einen eigenen controller hat
            // wo dann die componentId für die link-komponente aber die componentClass der News-Link Komponente daher kommt
            // das ganze muss statisch gemacht werden, da die link-komponente möglicherweise noch nicht gespeichert wurde
            if (!$allowCheck) {
                $allowCheck = $this->_canHaveChildComponentOnSamePage($component->componentClass, $class);
            }

            // sobald man eine bearbeiten darf, darf man alle bearbeiten
            // zB wenn man bei proSalzburg und proPona gleichzeitig drin ist
            if ($allowCheck && $this->getComponentAcl()->isAllowed($authData, $component))
                return true;
        }
        return false;
    }

    private function _canHaveChildComponentOnSamePage($componentClass, $lookForClass)
    {
        static $cache = array();
        if (isset($cache[$componentClass.'-'.$lookForClass])) {
            return $cache[$componentClass.'-'.$lookForClass];
        }
        $cache[$componentClass.'-'.$lookForClass] = false;
        $childComponentClasses = Vpc_Abstract::getChildComponentClasses(
            $componentClass, array('page' => false)
        );
        if (in_array($lookForClass, $childComponentClasses)) {
            $cache[$componentClass.'-'.$lookForClass] = true;
            return true;
        }
        foreach ($childComponentClasses as $c) {
            if ($this->_canHaveChildComponentOnSamePage($c, $lookForClass)) return true;
        }
        return false;
    }
}
