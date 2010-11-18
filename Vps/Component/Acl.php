<?php
class Vps_Component_Acl
{
    private $_isAllowedComponentClassCache = array();
    protected $_roleRegistry;
    protected $_rules = array(
        'allTags' => array(
            'allRoles' => array(
                'type' => Vps_Acl::TYPE_DENY
            )
        ),
        'byTagId' => array(),
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
        $this->allowTag('admin', null);
        $this->allowComponent('admin', null);
        if ($this->_roleRegistry->has('superuser')) {
            $this->allowTag('superuser', null);
            $this->allowComponent('superuser', null);
        }
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

    //beim überschreiben aufpassen wegen dem _isAllowedComponentClassCache
    //darum erstmal private gemacht
    private function _isAllowedComponentClass($userRow, $componentClass)
    {
        $role = $this->_getRole($userRow);

        $rules = $this->_getRules('Component', $componentClass, $role);
        if ($rules && $rules['type'] == Vps_Acl::TYPE_ALLOW) return true;
        if ($rules && $rules['type'] == Vps_Acl::TYPE_DENY) return false;

        $rules = $this->_getRules('Component', null, $role);
        if ($rules && $rules['type'] == Vps_Acl::TYPE_ALLOW) return true;
        if ($rules && $rules['type'] == Vps_Acl::TYPE_DENY) return false;

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

    protected function _isAllowedComponentData($userRow, Vps_Component_Data $component)
    {
        $role = $this->_getRole($userRow);

        $allowed = false;
        while ($component) {
            if (isset($component->tags) && $component->tags) {
                foreach ($component->tags as $t) {
                    $rules = $this->_getRules('Tag', $t, $role);
                    if ($rules && $rules['type'] == Vps_Acl::TYPE_ALLOW) {
                        $allowed = true;
                        break;
                    }
                }
            }
            if ($component && $component->isPage) break;
            $component = $component->parent;
        }
        if (!$allowed) {
            $rules = $this->_getRules('Tag', null, $role);
            if ($rules && $rules['type'] == Vps_Acl::TYPE_ALLOW) {
                $allowed = true;
            }
        }
        return $allowed;
    }

    public function allowTag($role, $tag, $privilege = null)
    {
        if ($privilege) throw new Vps_Exception("Not yet implemented");
        if (!is_null($role)) $role = $this->_roleRegistry->get($role);
        $rules =& $this->_getRules('Tag', $tag, $role, true);
        $rules['type'] = Vps_Acl::TYPE_ALLOW;
        return $this;
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

    protected function &_getRules($type, $tag, Zend_Acl_Role_Interface $role = null, $create = false)
    {
        // create a reference to null
        $null = null;
        $nullRef =& $null;

        // follow $resource
        do {
            if (null === $tag) {
                $visitor =& $this->_rules['all'.$type.'s'];
                break;
            }
            if (!isset($this->_rules['by'.$type.'Id'][$tag])) {
                if (!$create) {
                    return $nullRef;
                }
                $this->_rules['by'.$type.'Id'][$tag] = array();
            }
            $visitor =& $this->_rules['by'.$type.'Id'][$tag];
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
