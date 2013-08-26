<?php
class Kwf_Acl extends Zend_Acl
{
    protected $_componentAclClass = 'Kwf_Component_Acl';

    /**
     * @var Kwf_Component_Acl
     */
    protected $_componentAcl;
    protected $_kwcResourcesLoaded = false;


    public static function getInstance()
    {
        static $i;
        if (!isset($i)) {
            $t = microtime(true);
            $m = memory_get_usage();
            $cacheId = 'acl';
            $i = Kwf_Cache_Simple::fetch($cacheId);
            if ($i === false) {
                $class = Kwf_Config::getValue('aclClass');
                $i = new $class();
                $i->loadKwcResources();
                Kwf_Cache_Simple::add($cacheId, $i);
                Kwf_Benchmark::subCheckpoint('create Acl', microtime(true)-$t);
            } else {
                Kwf_Benchmark::subCheckpoint('load cached Acl '.round((memory_get_usage()-$m) / (1024*1024), 2).'MB', microtime(true)-$t);
            }
        }
        return $i;
    }

    public static function clearCache()
    {
        static $cleared = false;
        if ($cleared) return; //only clear single time
        $cleared = true;

        $cacheId = 'acl';
        Kwf_Cache_Simple::delete($cacheId);
    }

    public function __construct()
    {
        $this->addRole(new Zend_Acl_Role('guest'));
        $this->addRole(new Kwf_Acl_Role_Admin('admin', 'Administrator'));
        $this->addRole(new Zend_Acl_Role('cli'));

        $this->add(new Zend_Acl_Resource('default_index'));
        $this->add(new Zend_Acl_Resource('kwf_user_menu'));
        $this->add(new Zend_Acl_Resource('kwf_user_login'));
        $this->add(new Zend_Acl_Resource('kwf_user_changeuser'));
        $this->add(new Zend_Acl_Resource('kwf_user_logout'));
        $this->add(new Zend_Acl_Resource('kwf_error_error'));
        $this->add(new Zend_Acl_Resource('kwf_user_about'));
        $this->add(new Zend_Acl_Resource('kwf_welcome_index'));
        $this->add(new Zend_Acl_Resource('kwf_welcome_content'));
        $this->add(new Zend_Acl_Resource('kwf_welcome_welcome'));
        $this->add(new Zend_Acl_Resource('kwf_debug'));
        $this->add(new Zend_Acl_Resource('kwf_debug_session-restart'), 'kwf_debug');
        $this->add(new Zend_Acl_Resource('kwf_debug_php-info'), 'kwf_debug');
        $this->add(new Zend_Acl_Resource('kwf_debug_apc'), 'kwf_debug');
        $this->add(new Zend_Acl_Resource('kwf_debug_assets-dependencies'), 'kwf_debug');
        $this->add(new Zend_Acl_Resource('kwf_debug_benchmark'), 'kwf_debug');
        $this->add(new Zend_Acl_Resource('kwf_debug_benchmark-counter'));
        $this->add(new Zend_Acl_Resource('kwf_media_upload'));
        $this->add(new Zend_Acl_Resource('kwf_test'));
        $this->add(new Zend_Acl_Resource('kwf_maintenance_setup'));
        $this->add(new Zend_Acl_Resource('kwf_maintenance_update'));
        $this->add(new Zend_Acl_Resource('kwf_maintenance_clear-cache'));
        $this->add(new Zend_Acl_Resource('kwf_maintenance_update-downloader'));
        $this->add(new Zend_Acl_Resource('kwf_maintenance_fulltext'));
        $this->add(new Zend_Acl_Resource('edit_role'));
        $this->add(new Kwf_Acl_Resource_EditRole('edit_role_admin', 'admin'), 'edit_role');

        $this->add(new Kwf_Acl_Resource_UserSelf('kwf_user_self', '/kwf/user/self'));

        $this->add(new Zend_Acl_Resource('kwf_spam_set'));

        $this->add(new Zend_Acl_Resource('kwf_cli'));
        $this->add(new Zend_Acl_Resource('kwf_cli_help'));
        $this->add(new Zend_Acl_Resource('kwf_cli_index'));
        $this->add(new Zend_Acl_Resource('kwf_cli_trlparse'));
        $this->add(new Zend_Acl_Resource('kwf_cli_hlpparse'));
        $this->add(new Zend_Acl_Resource('kwf_test_connectionerror'));
        $this->allow('cli', 'kwf_cli');
        $this->allow('cli', 'kwf_cli_help');
        $this->allow('cli', 'kwf_cli_index');
        $this->allow('cli', 'kwf_cli_trlparse');
        $this->allow('cli', 'kwf_cli_hlpparse');

        $this->allow(null, 'default_index');
        $this->allow(null, 'kwf_test_connectionerror');
        $this->deny('guest', 'default_index');
        $this->allow(null, 'kwf_user_menu');
        $this->allow(null, 'kwf_user_login');
        $this->allow(null, 'kwf_user_logout');
        $this->allow(null, 'kwf_error_error');
        $this->allow(null, 'kwf_user_about');
        $this->allow(null, 'kwf_welcome_index');
        $this->allow(null, 'kwf_welcome_content');
        $this->allow(null, 'kwf_welcome_welcome');
        $this->deny('guest', 'kwf_welcome_index');
        $this->allow(null, 'kwf_user_self');
        $this->deny('guest', 'kwf_user_self');
        $this->allow('admin', 'kwf_debug');
        $this->allow(null, 'kwf_media_upload');
        $this->allow('admin', 'edit_role');
        $this->allow(null, 'kwf_spam_set');
        $this->allow(null, 'kwf_debug_session-restart');
        $this->allow(null, 'kwf_test');
        $this->allow(null, 'kwf_maintenance_setup'); //allow for everyone, as there are no users yet during setup
        $this->allow('admin', 'kwf_maintenance_update');
        $this->allow('admin', 'kwf_maintenance_clear-cache');
        $this->allow('admin', 'kwf_maintenance_update-downloader');
        $this->allow('admin', 'kwf_maintenance_fulltext');
        $this->allow(null, 'kwf_debug_benchmark-counter'); //allow for everyone we do additional permissions check in there
    }

    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        $this->loadKwcResources();

        $ret = parent::isAllowed($role, $resource, $privilege);

        if (!$ret) {
            if (null !== $resource) {
                $resource = $this->get($resource);
            }

            if ($resource instanceof Kwf_Acl_Resource_MenuDropdown) {
                foreach ($this->getResources($resource) as $r) {
                    if ($r instanceof Kwf_Acl_Resource_MenuUrl
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
            $userModel = Kwf_Registry::get('userModel');
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
            if ($r instanceof Kwf_Acl_Role_Additional
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
            if ($r instanceof Kwf_Acl_Resource_EditRole
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
            if ($role instanceof Kwf_Acl_Role && !($role instanceof Kwf_Acl_Role_Additional)
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
            if ($role instanceof Kwf_Acl_Role_Additional) {
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
        $this->loadKwcResources();
        $ret = array();
        foreach ($this->_resources as $resource) {
            $ret[] = $resource['instance'];
        }
        return $ret;
    }

    protected function _getRoleRegistry()
    {
        if (null === $this->_roleRegistry) {
            $this->_roleRegistry = new Kwf_Acl_Role_Registry();
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
    public function loadKwcResources()
    {
        if ($this->_kwcResourcesLoaded) return;
        $this->_kwcResourcesLoaded = true;

        $t = microtime(true);
        $menuConfigs = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (Kwc_Abstract::getFlag($c, 'hasResources')) {
                Kwc_Admin::getInstance($c)->addResources($this);
            }
            if (Kwc_Abstract::hasSetting($c, 'menuConfig') && Kwc_Abstract::getSetting($c, 'menuConfig')) {
                $menuConfigs[] = Kwf_Component_Abstract_MenuConfig_Abstract::getInstance($c);
            }
        }
        usort($menuConfigs, array(get_class($this), '_compareMenuConfig'));
        foreach ($menuConfigs as $cfg) {
            $cfg->addResources($this);
        }
        Kwf_Benchmark::subCheckpoint('kwc resources', microtime(true)-$t);
    }

    public static function _compareMenuConfig(Kwf_Component_Abstract_MenuConfig_Abstract $a, Kwf_Component_Abstract_MenuConfig_Abstract $b)
    {
        return ($a->getPriority() < $b->getPriority() ? -1 : 1);
    }

    public function getMenuConfig($user)
    {
        $this->loadKwcResources();
        return $this->_processResources($user, $this->getResources());
    }

    private function _processResources($user, $resources)
    {
        $menus = array();
        foreach ($resources as $resource) {
            if ($resource instanceof Kwf_Acl_Resource_MenuDropdown) {
                // don't validate because visibility of a dropdown should be defined
                // by it's children
            } else if ($resource instanceof Kwf_Acl_Resource_Component_Interface) {
                if (!$this->getComponentAcl()->isAllowed($user, $resource->getComponent())) continue;
            } else if ($resource instanceof Kwf_Acl_Resource_ComponentClass_Interface) {
                if (!$this->getComponentAcl()->isAllowed($user, $resource->getComponentClass())) continue;
            } else {
                if (!$this->isAllowedUser($user, $resource, 'view')) continue;
            }
            if (!$resource instanceof Kwf_Acl_Resource_Abstract) {
                //nur Kwf-Resourcen im Menü anzeigen
                $menus = array_merge($menus, $this->_processResources($user, $this->getResources($resource)));
                continue;
            }
            $menu = array();
            $menu['menuConfig'] = $resource->getMenuConfig();
            if (is_string($menu['menuConfig'])) {
                $menu['menuConfig'] = array('text' => $menu['menuConfig']);
            }
            $menu['menuConfig']['text'] = Kwf_Trl::getInstance()->trlStaticExecute($menu['menuConfig']['text']);

            if (isset($menu['menuConfig']['icon'])) {
                if (is_string($menu['menuConfig']['icon'])) {
                    $menu['menuConfig']['icon'] = new Kwf_Asset($menu['menuConfig']['icon']);
                }
                $menu['menuConfig']['icon'] = $menu['menuConfig']['icon']->__toString();
            }

            if ($resource instanceof Kwf_Acl_Resource_MenuDropdown) {
                $menu['type'] = 'dropdown';
                $menu['children'] = $this->_processResources($user, $this->getResources($resource));
                if (!$menu['children']) {
                    //wenn keine children dropdown ignorieren
                    continue;
                }
                if (count($menu['children']) == 1 && $resource->getCollapseIfSingleChild()) {
                    $m = $menu;
                    $menu = $menu['children'][0];
                    $menu['menuConfig'] = $m['menuConfig'];
                }
            } else if ($resource instanceof Kwf_Acl_Resource_MenuEvent) {
                $menu['type'] = 'event';
                $menu['eventConfig'] = $resource->getMenuEventConfig();
            } else if ($resource instanceof Kwf_Acl_Resource_MenuUrl) {
                $menu['type'] = 'url';
                $menu['url'] = $resource->getMenuUrl();
            } else if ($resource instanceof Kwf_Acl_Resource_MenuCommandDialog) {
                $menu['type'] = 'commandDialog';
                $menu['commandClass'] = $resource->getMenuCommandClass();
                $menu['commandConfig'] = $resource->getMenuCommandConfig();
            } else if ($resource instanceof Kwf_Acl_Resource_MenuCommand) {
                $menu['type'] = 'command';
                $menu['commandClass'] = $resource->getMenuCommandClass();
                $menu['commandConfig'] = $resource->getMenuCommandConfig();
            } else if ($resource instanceof Kwf_Acl_Resource_MenuSeparator) {
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
        if (!Kwf_Component_Data_Root::getComponentClass()) return null;
        if (!isset($this->_componentAcl)) {
            $this->_componentAcl = new $this->_componentAclClass($this->_getRoleRegistry());
        }
        return $this->_componentAcl;
    }

    public function setComponentAcl(Kwf_Component_Acl $componentAcl)
    {
        $this->_componentAcl = $componentAcl;
    }

    public function setComponentAclClass($class)
    {
        if (isset($this->_componentAcl)) {
            throw new Kwf_Exception("Can't modify componentAclClass when getComponentAcl was called already");
        }
        $this->_componentAclClass = $class;
    }

    public function isAllowedComponent($class, $authData)
    {
        $allowed = false;
        foreach ($this->getAllResources() as $r) {
            if ($r instanceof Kwf_Acl_Resource_ComponentClass_Interface) {
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
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByDbId($componentId, array('ignoreVisible'=>true));
        if (!$components) {
            throw new Kwf_Exception("Can't find component to check permissions");
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
                    $allowedComponentClasses = Kwc_Abstract::getChildComponentClasses(
                        $c->componentClass, array('page' => false)
                    );
                    $allowCheck = in_array($class, $allowedComponentClasses);
                    $c = $c->parent;
                }
            }

            // SharedDataClass braucht Sonderbehandlung, weil class die Komponente ist
            // und componentId aber auf die Shared-Komponente zeigt
            if (!$allowCheck) {
                $sharedDataClass = Kwc_Abstract::getFlag($class, 'sharedDataClass');
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
        $childComponentClasses = Kwc_Abstract::getChildComponentClasses(
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


    public function allow($roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null)
    {
        if ($resources == null && $roles != 'admin') {
            throw new Kwf_Exception("Don't be lazy, never allow all resources - you should whitelist");
        }
        return parent::allow($roles, $resources, $privileges, $assert);
    }

    public function setParentResource($resource, $parent)
    {
        if (is_string($resource)) {
            $resource = new Zend_Acl_Resource($resource);
        }

        if (!$resource instanceof Zend_Acl_Resource_Interface) {
            require_once 'Zend/Acl/Exception.php';
            throw new Zend_Acl_Exception('addResource() expects $resource to be of type Zend_Acl_Resource_Interface');
        }

        $resourceId = $resource->getResourceId();

        if (!$this->has($resourceId)) {
            require_once 'Zend/Acl/Exception.php';
            throw new Zend_Acl_Exception("Resource id '$resourceId' doesn't exists in the ACL");
        }

        //unset children of previous parent
        $previousParent = $this->_resources[$resourceId]['parent'];
        if ($previousParent) {
            unset($this->_resources[$previousParent->getResourceId()]['children'][$resourceId]);
        }

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
                require_once 'Zend/Acl/Exception.php';
                throw new Zend_Acl_Exception("Parent Resource id '$resourceParentId' does not exist", 0, $e);
            }
            $this->_resources[$resourceParentId]['children'][$resourceId] = $resource;
        }

        $this->_resources[$resourceId]['parent'] = $resourceParent;

        return $this;
    }
}
