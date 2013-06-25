<?php
class Kwf_Component_Acl
{
    private $_isAllowedComponentClassCache = array();
    private $_allowedRecursiveChildComponentsCache = array();
    protected $_roleRegistry;
    protected $_rules = array(
        'allComponents' => array(
            'allRoles' => array(
                'type' => Kwf_Acl::TYPE_DENY
            )
        ),
        'allComponentsRecursive' => array(
            'allRoles' => array(
                'type' => Kwf_Acl::TYPE_DENY
            )
        ),
        'byComponentId' => array(),
        'byComponentRecursiveId' => array()
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
     * @param Kwf_Component_Data/string
     * @param string
     * @return bool
     */
    public function isAllowed($userRow, $component, $privilege = null)
    {
        $role = $this->_getRole($userRow);

        if ($component instanceof Kwf_Component_Data) {
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
        if ($this->_isAllowedComponentClassByRole($this->_getRole($userRow), $componentClass)) {
            return true;
        }
        foreach ($this->_getAdditionalRoles($userRow) as $role) {
            if ($this->_isAllowedComponentClassByRole($role, $componentClass)) {
                return true;
            }
        }
        return false;
    }

    private function _isAllowedComponentClassByRole($role, $componentClass)
    {
        $role = $this->_roleRegistry->get($role);

        //also check recursive as we allow editing child components of same page
        $ret = $this->_isAllowedComponentClassNonRek('Component', $role, $componentClass);
        if (!is_null($ret)) return $ret;

        $ret = $this->_isAllowedComponentClassNonRek('ComponentRecursive', $role, $componentClass);
        if (!is_null($ret)) return $ret;

        //überklassen überprüfen
        //cache ist nötig wegen endlos-rekursion + performance
        $roleId = $role->getRoleId();
        if (isset($this->_isAllowedComponentClassCache[$roleId][$componentClass])) {
            return $this->_isAllowedComponentClassCache[$roleId][$componentClass];
        }
        $this->_isAllowedComponentClassCache[$roleId][$componentClass] = false;
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            foreach (Kwc_Abstract::getChildComponentClasses($c) as $cc) {
                if ($cc == $componentClass) {
                    if ($this->_isAllowedComponentClassByRole($role, $c)) {
                        $this->_isAllowedComponentClassCache[$roleId][$componentClass] = true;
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function _isAllowedComponentClassNonRek($type, $role, $componentClass)
    {
        $rules = $this->_getRules($type, $componentClass, $role);
        if ($rules && $rules['type'] == Kwf_Acl::TYPE_ALLOW) return true;
        if ($rules && $rules['type'] == Kwf_Acl::TYPE_DENY) return false;

        $rules = $this->_getRules($type, null, $role);
        if ($rules && $rules['type'] == Kwf_Acl::TYPE_ALLOW) return true;
        if ($rules && $rules['type'] == Kwf_Acl::TYPE_DENY) return false;

        return null;
    }

    protected function _isAllowedComponentData($userRow, Kwf_Component_Data $component)
    {
        if ($this->_isAllowedComponentDataByRole($this->_getRole($userRow), $component)) {
            return true;
        }
        foreach ($this->_getAdditionalRoles($userRow) as $role) {
            if ($this->_isAllowedComponentDataByRole($role, $component)) {
                return true;
            }
        }
        return false;
    }

    protected function _isAllowedComponentDataByRole($role, Kwf_Component_Data $component)
    {
        $role = $this->_roleRegistry->get($role);
        $outsidePage = false;
        while ($component) { // irgendeine Komponente auf dem Weg nach oben muss allowed sein
            if (!$outsidePage) {
                $allowed = $this->_isAllowedComponentClassNonRek('Component', $role, $component->componentClass);
                if ($allowed) return true;
            }
            $allowed = $this->_isAllowedComponentClassNonRek('ComponentRecursive', $role, $component->componentClass);
            if ($allowed) return true;
            if ($component && $component->isPseudoPage) {
                $outsidePage = true;
            }
            $component = $component->parent;
        }
        return false;
    }

    private function _getAdditionalRoles($user)
    {
        $ret = array();
        if (!is_null($user) && !is_string($user)) {
            $additionalRoles = array();
            foreach ($this->_roleRegistry->getRoles() as $r) {
                if ($r instanceof Kwf_Acl_Role_Additional &&
                    $r->getParentRoleId() == $this->_getRole($user)
                ) {
                    $additionalRoles[] = $r->getRoleId();
                }
            }
            if ($additionalRoles) {
                foreach ($user->getAdditionalRoles() as $role) {
                    if (in_array($role, $additionalRoles)) {
                        $ret[] = $role;
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * Gibt alle Komponenten zurück die im Seitenbaum bearbeitet werden dürfen
     *
     * alles was unter einer seite liegt die im seitenbaum angezeigt wird
     *
     * Langsam
     */
    public function getAllowedRecursiveChildComponents($userRow)
    {
        $cacheId = is_object($userRow) ? $userRow->id : $userRow;
        if (!isset($this->_allowedRecursiveChildComponentsCache[$cacheId])) {
            $allowedComponentClasses = $this->_getAllowedComponentClasses($userRow);
            $ret = array();
            $cmps = Kwf_Component_Data_Root::getInstance()->getRecursiveChildComponents(array(
                'ignoreVisible'=>true,
                'componentClasses' => $allowedComponentClasses,
            ), array(
                'ignoreVisible'=>true,
                'generatorFlags' => array('showInPageTreeAdmin' => true),
            ));
            foreach ($cmps as $c) {
                if ($this->isAllowed($userRow, $c)) $ret[] = $c;
            }
            $this->_allowedRecursiveChildComponentsCache[$cacheId] = $ret;
        }
        return $this->_allowedRecursiveChildComponentsCache[$cacheId];
    }

    /**
     * Gibt alle Unterkomponenten einer Seite zurück die barbeitet werden dürfen
     *
     * d.h. alles *bis* zur pseudoPage oder showInPageTreeAdmin
     */
    public function getAllowedChildComponents($userRow, $component)
    {
        $allowedComponentClasses = $this->_getAllowedComponentClasses($userRow);
        return $component->getRecursiveChildComponents(array(
            'componentClasses' => $allowedComponentClasses,
            'ignoreVisible' => true,
            'pseudoPage' => false,
            'generatorFlags' => array('showInPageTreeAdmin' => false),
        ), array(
            'ignoreVisible' => true,
            'pseudoPage' => false,
            'generatorFlags' => array('showInPageTreeAdmin' => false),
        ));
    }

    /**
     * @return array array mit klassen die erlaubt sind
     */
    protected function _getAllowedComponentClasses($userRow)
    {
        $ret = $this->_getAllowedComponentClassesByType($userRow, 'Component');
        $ret = array_merge($ret, $this->_getAllowedComponentClassesByType($userRow, 'ComponentRecursive'));
        return $ret;
    }

    private function _getAllowedComponentClassesByType($userRow, $type)
    {
        $role = $this->_getRole($userRow)->getRoleId();

        $ret = array();
        $r = $this->_getRules($type, null, $this->_getRole($userRow));
        if (isset($r['type']) && $r['type'] == Kwf_Acl::TYPE_ALLOW) {
            throw new Kwf_Exception("don't do that, it's slow");
            //alles erlaubt
            return null;
        }

        foreach ($this->_rules['by'.$type.'Id'] as $componentClass => $rights) {
            if (isset($rights['byRoleId'][$role])) {
                $r = $rights['byRoleId'][$role];
                if ($r['type'] == Kwf_Acl::TYPE_ALLOW) {
                    $ret[] = $componentClass;
                } else if ($r['type'] == Kwf_Acl::TYPE_DENY) {
                    throw new Kwf_Exception_NotYetImplemented('Klasseneinschränkung wird noch nicht unterstützt.');
                }
            }
        }

        return $ret;
    }

    /**
     * Allow Component plus child components on same page
     */
    public function allowComponent($role, $componentClass, $privilege = null)
    {
        if ($privilege) throw new Kwf_Exception("Not yet implemented");
        if (!is_null($role)) $role = $this->_roleRegistry->get($role);
        $rules =& $this->_getRules('Component', $componentClass, $role, true);
        $rules['type'] = Kwf_Acl::TYPE_ALLOW;
        return $this;
    }

    public function denyComponent($role, $componentClass, $privilege = null)
    {
        throw new Kwf_Exception_NotYetImplemented("das gehört mit einem praktischen anwendungsbeispiel durchdacht");
        if ($privilege) throw new Kwf_Exception("Not yet implemented");
        if (!is_null($role)) $role = $this->_roleRegistry->get($role);
        $rules =& $this->_getRules('Component', $componentClass, $role, true);
        $rules['type'] = Kwf_Acl::TYPE_DENY;
        return $this;
    }

    /**
     * Allow Component plus child components including all child pages
     */
    public function allowComponentRecursive($role, $componentClass, $privilege = null)
    {
        if ($privilege) throw new Kwf_Exception("Not yet implemented");
        if (!is_null($role)) $role = $this->_roleRegistry->get($role);
        $rules =& $this->_getRules('ComponentRecursive', $componentClass, $role, true);
        $rules['type'] = Kwf_Acl::TYPE_ALLOW;
        return $this;
    }

    public function denyComponentRecursive($role, $componentClass, $privilege = null)
    {
        throw new Kwf_Exception_NotYetImplemented("das gehört mit einem praktischen anwendungsbeispiel durchdacht");
        if ($privilege) throw new Kwf_Exception("Not yet implemented");
        if (!is_null($role)) $role = $this->_roleRegistry->get($role);
        $rules =& $this->_getRules('ComponentRecursive', $componentClass, $role, true);
        $rules['type'] = Kwf_Acl::TYPE_DENY;
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
