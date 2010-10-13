<?php
class Vps_Component_Acl
{
    private $_isAllowedComponentClassCache = array();
    private $_allowedRecursiveChildComponentsCache = array();
    protected $_roleRegistry;
    protected $_rules = array(
        'allComponents' => array(
            'allRoles' => array(
                'type' => Vps_Acl::TYPE_DENY
            )
        ),
        'byComponentId' => array()
    );

    public function __construct(Zend_Acl_Role_Registry $roleRegistry)
    {
        $this->_roleRegistry = $roleRegistry;
        $this->_init();
    }


    protected function _init()
    {
        $this->allowComponent('admin', null);
        if ($this->_roleRegistry->has('superuser')) {
            $this->allowComponent('superuser', null);
        }
    }

    protected function _getRole($userRow)
    {
        if (is_null($userRow)) {
            $role = 'guest';
        } else if (is_string($userRow)) {
            $role = $userRow;
        } else {
            $role = $userRow->role;
        }
        return $this->_roleRegistry->get($role);
    }

    /**
     * @param User-Row / null für guest
     * @param Vps_Component_Data/string
     * @param string
     * @return bool
     */
    public function isAllowed($userRow, $component, $privilege = null)
    {
        $role = $this->_getRole($userRow);

        if ($component instanceof Vps_Component_Data) {
            $componentClass = $component->componentClass;
            $allowed = $this->_isAllowedComponentData($userRow, $component);
            if (!$allowed) return false;
        } else {
            $componentClass = $component;
        }

        $allowed = $this->_isAllowedComponentClass($userRow, $componentClass);
        if (!$allowed) return false;

        return true;
    }

    //beim überschreiben aufpassen wegen dem _isAllowedComponentClassCache
    //darum erstmal private gemacht
    private function _isAllowedComponentClass($userRow, $componentClass)
    {
        $role = $this->_getRole($userRow);

        $ret = $this->_isAllowedComponentClassNonRek($role, $componentClass);
        if (!is_null($ret)) return $ret;

        //überklassen überprüfen
        //cache ist nötig wegen endlos-rekursion + performance
        $roleId = $role->getRoleId();
        if (isset($this->_isAllowedComponentClassCache[$roleId][$componentClass])) {
            return $this->_isAllowedComponentClassCache[$roleId][$componentClass];
        }
        $this->_isAllowedComponentClassCache[$roleId][$componentClass] = false;
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            foreach (Vpc_Abstract::getChildComponentClasses($c) as $cc) {
                if ($cc == $componentClass) {
                    if ($this->_isAllowedComponentClass($userRow, $c)) {
                        $this->_isAllowedComponentClassCache[$roleId][$componentClass] = true;
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function _isAllowedComponentClassNonRek($role, $componentClass)
    {
        $rules = $this->_getRules('Component', $componentClass, $role);
        if ($rules && $rules['type'] == Vps_Acl::TYPE_ALLOW) return true;
        if ($rules && $rules['type'] == Vps_Acl::TYPE_DENY) return false;

        $rules = $this->_getRules('Component', null, $role);
        if ($rules && $rules['type'] == Vps_Acl::TYPE_ALLOW) return true;
        if ($rules && $rules['type'] == Vps_Acl::TYPE_DENY) return false;
    }

    protected function _isAllowedComponentData($userRow, Vps_Component_Data $component)
    {
        $role = $this->_getRole($userRow);
        while ($component) { // irgendeine Komponente auf dem Weg nach oben muss allowed sein
            $allowed = $this->_isAllowedComponentClassNonRek($role, $component->componentClass);
            if ($allowed) return true;

            //TODO: wenn alle unterseiten auch berechtigung haben sollen brauchen wir sowas wie allowComponentRecursive
            //wenns nur eine Detail gibt kann diese extra dazugeschalten werden
            if ($component && $component->isPseudoPage) break;

            $component = $component->parent;
        }
        return false;
    }

    // Langsam
    public function getAllowedRecursiveChildComponents($userRow)
    {
        $cacheId = is_object($userRow) ? $userRow->id : $userRow;
        if (!isset($this->_allowedRecursiveChildComponentsCache[$cacheId])) {
            $allowedComponentClasses = $this->_getAllowedComponentClasses($userRow);
            $ret = array();
            $cmps = Vps_Component_Data_Root::getInstance()->getComponentsByClass($allowedComponentClasses, array('ignoreVisible'=>true));
            foreach ($cmps as $c) {
                if ($this->isAllowed($userRow, $c)) $ret[] = $c;
            }
            $this->_allowedRecursiveChildComponentsCache[$cacheId] = $ret;
        }
        return $this->_allowedRecursiveChildComponentsCache[$cacheId];
    }

    public function getAllowedChildComponents($userRow, $component)
    {
        $allowedComponentClasses = $this->_getAllowedComponentClasses($userRow);
        return $component->getRecursiveChildComponents(array(
            'componentClasses' => $allowedComponentClasses,
            'ignoreVisible' => true,
            'pseudoPage' => false,
            'flags' => array('showInPageTreeAdmin' => false),
        ), array(
            'pseudoPage' => false,
            'flags' => array('showInPageTreeAdmin' => false),
        ));
    }

    protected function _getAllowedComponentClasses($userRow)
    {
        $role = $this->_getRole($userRow);

        $ret = array();
        $r = $this->_getRules('Component', null, $this->_getRole($userRow));
        if (isset($r['type']) && $r['type'] == Vps_Acl::TYPE_ALLOW) {
            $ret = null;
        }

        $role = $role->getRoleId();
        foreach ($this->_rules['byComponentId'] as $componentClass => $rights) {
            if (isset($rights['byRoleId'][$role])) {
                $r = $rights['byRoleId'][$role];
                if ($r['type'] == Vps_Acl::TYPE_ALLOW) {
                    if (!is_array($ret)) $ret = array();
                    $ret[] = $componentClass;
                } else if ($r['type'] == Vps_Acl::TYPE_DENY) {
                    throw new Vps_Exception_NotYetImplemented('Klasseneinschränkung wird noch nicht unterstützt.');
                }
            }
        }

        return $ret;
    }

    public function allowComponent($role, $componentClass, $privilege = null)
    {
        if ($privilege) throw new Vps_Exception("Not yet implemented");
        if (!is_null($role)) $role = $this->_roleRegistry->get($role);
        $rules =& $this->_getRules('Component', $componentClass, $role, true);
        $rules['type'] = Vps_Acl::TYPE_ALLOW;
        return $this;
    }

    public function denyComponent($role, $componentClass, $privilege = null)
    {
        if ($privilege) throw new Vps_Exception("Not yet implemented");
        if (!is_null($role)) $role = $this->_roleRegistry->get($role);
        $rules =& $this->_getRules('Component', $componentClass, $role, true);
        $rules['type'] = Vps_Acl::TYPE_DENY;
        return $this;
    }

    protected function &_getRules($type, $name, Zend_Acl_Role_Interface $role = null, $create = false)
    {
        // create a reference to null
        $null = null;
        $nullRef =& $null;

        // follow $resource
        do {
            if (null === $name) {
                $visitor =& $this->_rules['all'.$type.'s'];
                break;
            }
            if (!isset($this->_rules['by'.$type.'Id'][$name])) {
                if (!$create) {
                    return $nullRef;
                }
                $this->_rules['by'.$type.'Id'][$name] = array();
            }
            $visitor =& $this->_rules['by'.$type.'Id'][$name];
        } while (false);


        // follow $role
        if (null === $role) {
            if (!isset($visitor['allRoles'])) {
                if (!$create) {
                    return $nullRef;
                }
                $visitor['allRoles'] = array();
            }
            return $visitor['allRoles'];
        }
        $roleId = $role->getRoleId();
        if (!isset($visitor['byRoleId'][$roleId])) {
            if (!$create) {
                return $nullRef;
            }
            $visitor['byRoleId'][$roleId] = array();
        }
        return $visitor['byRoleId'][$roleId];
    }
}
