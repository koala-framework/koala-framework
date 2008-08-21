<?php
class Vps_Component_Data
{
    private $_component;

    private $_url;
    private $_rel;
    private $_filename;
    private $_constraintsCache = array();
    private $_generatorsCache = array();

    public function __construct($config)
    {
        foreach ($config as $k=>$i) {
            if ($k == 'url') {
                $this->_url = $i;
            } else if ($k == 'rel') {
                $this->_rel = $i;
            } else if ($k == 'filename') {
                $this->_filename = $i;
            } else {
                $this->$k = $i;
            }
        }
        if (!isset($this->dbId) && isset($this->componentId)) {
            $this->dbId = $this->componentId;
        }
        Vps_Benchmark::count('componentDatas', $this->componentId);
    }

    public function __get($var)
    {
        if ($var == 'url') {
            $filenames = array();
            $page = $this->getPage();
            do {
                if (Vpc_Abstract::getFlag($page->componentClass, 'shortcutUrl')) {
                    $filenames[] = call_user_func(array($page->componentClass, 'getShortcutUrl'), $page->componentClass, $page);
                    break;
                } else {
                    $filenames[] = $page->filename;
                }
            } while ($page = $page->getParentPseudoPage());
            return '/'.implode('/', array_reverse($filenames));
        } else if ($var == 'rel') {
            $childs = $this->getPage()->getRecursiveChildComponents(array(
                'flags' => array('noIndex' => true),
                'page' => false,
                'limit' => 1
            ));
            $rel = $this->getPage()->_rel;
            if ($childs || $this->_hasFlags($this->componentClass, array('noIndex' => true))) {
                $rel .= ' nofollow';
            }
            return trim($rel);
        } else if ($var == 'filename') {
            return $this->getPseudoPage()->_filename;
        } else {
            throw new Vps_Exception("Variable '$var' is not set for ".get_class($this) . " with componentId '{$this->componentId}'");
        }
    }

    public function __isset($var)
    {
        if ($var == 'url' || $var == 'rel' || $var == 'filename') {
            return true;
        }
        return false;
    }

    public function __unset($var)
    {
        if ($var == 'url' || $var == 'rel' || $var == 'filename') {
            throw new Vps_Exception("Variable '$var' can't be modified for ".get_class($this));
        } else {
            throw new Vps_Exception("Variable '$var' is not set for ".get_class($this));
        }
    }

    public function __set($var, $value)
    {
        if ($var == 'url' || $var == 'rel' || $var == 'filename') {
            throw new Vps_Exception("Variable '$var' can't be modified for ".get_class($this));
        } else {
            $this->$var = $value;
        }
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'set') {
            if (!isset($arguments[0]) && !is_null($arguments[0])) {
                throw new Vps_Exception("Missing argument 1 (value)");
            }
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            $this->$name = $arguments[0];
            return $this;
        } else if (substr($method, 0, 3) == 'get') {
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->$name;
        } else {
            throw new Vps_Exception("Invalid method called: '$method'");
        }
    }

    public function getRecursiveGenerators(array $constraints,
                                array $childConstraints = array('page'=>false))
    {
        $ret = $this->getGenerators($constraints);
        foreach ($this->getChildComponents($this->_formatChildConstraints($constraints, $childConstraints)) as $component) {
            $ret = array_merge($ret, $component->getRecursiveGenerators($constraints, $childConstraints));
        }
        return $ret;
    }

    public function getGenerators($constraints = array())
    {
        $sc = '';
        foreach ($constraints as $key => $val) {
            $sc .= $key . $val;
        }
        $sc = md5($sc);
        if (!isset($this->_generatorsCache[$sc])) {
            $this->_generatorsCache[$sc] = Vps_Component_Generator_Abstract::getInstances($this->componentClass, $this, $constraints);
        }
        return $this->_generatorsCache[$sc];
    }

    private function _formatChildConstraints($constraints, $childConstraints)
    {
        if (isset($constraints['page']) && $constraints['page']) {
            $generatorInterface = 'Vps_Component_Generator_Page_Interface';
        } else if (isset($constraints['pseudoPage']) && $constraints['pseudoPage']) {
            $generatorInterface = 'Vps_Component_Generator_PseudoPage_Interface';
        } else if (isset($constraints['box']) && $constraints['box']) {
            $generatorInterface = 'Vps_Component_Generator_Box_Interface';
        } else if (isset($constraints['multibox']) && $constraints['multibox']) {
            $generatorInterface = 'Vps_Component_Generator_MultiBox_Interface';
        } else {
            $generatorInterface = false;
        }
        if ($generatorInterface) {
            if (isset($childConstraints['componentClass'])) {
                throw new Vps_Exception("Constraint 'page' or 'box' may not (yet) be used with 'componentClass'");
            }
            $childConstraints['componentClass'] = array();
            $classes = Vpc_Abstract::getChildComponentClasses($this->componentClass);
            foreach ($classes as $class) {
                if ($this->_hasGenerator($class, $generatorInterface)) {
                    $childConstraints['componentClass'][] = $class;
                }
            }
        }
        if (isset($constraints['hasEditComponents'])) {
            if (isset($childConstraints['componentClass'])) {
                throw new Vps_Exception("Constraint 'hasEditComponents' may not (yet) be used with 'componentClass'");
            }
            $childConstraints['componentClass'] = array();
            $classes = Vpc_Abstract::getChildComponentClasses($this->componentClass);
            foreach ($classes as $class) {
                if ($class && $this->_hasChildSetting($class, 'editComponents')) {
                    $childConstraints['componentClass'][] = $class;
                }
            }
        }
        if (isset($constraints['flags'])) {
            if (!is_array($constraints['flags'])) {
                throw new Vps_Exception("Constraint 'flags' must be of type array");
            }
            if (isset($childConstraints['componentClass'])) {
                throw new Vps_Exception("Constraint 'flags' may not (yet) be used with 'componentClass'");
            }
            $childConstraints['componentClass'] = array();
            $classes = Vpc_Abstract::getChildComponentClasses($this->componentClass, $constraints);
            foreach ($classes as $class) {
                if ($class && $this->_hasChildFlags($class, $constraints)) {
                    $childConstraints['componentClass'][] = $class;
                }
            }
        }
        if (isset($constraints['inherit'])) {
            $childConstraints['componentClass'] = array();
            $components = array();
            $generators = Vpc_Abstract::getSetting($this->componentClass, 'generators');
            foreach ($generators as $generator) {
                if (isset($generator['inherit']) && $generator['inherit']) {
                    $components = $generator['component'];
                    if (!is_array($components)) $components = array($generator['component']);
                }
            }
            while (!empty($components)) {
                $childComponents = array();
                foreach ($components as $component) {
                    $generators = Vpc_Abstract::getSetting($component, 'generators');
                    foreach ($generators as $generator) {
                        if (isset($generator['inherit']) && $generator['inherit']) {
                            $c = $generator['component'];
                            if (!is_array($c)) $c = array($generator['component']);
                            $childConstraints['componentClass'] = array_merge(
                                $childConstraints['componentClass'], $c
                            );
                            $childComponents = array_merge($childComponents, $c);
                        }
                    }
                }
                $components = $childComponents;
            }
        }
        return $childConstraints;
    }

    public function getRecursiveChildComponents(array $constraints,
                                array $childConstraints = array('page'=>false))
    {
        $ret = $this->getChildComponents($constraints);
        foreach ($this->getChildComponents($this->_formatChildConstraints($constraints, $childConstraints)) as $component) {
            $ret = array_merge($ret, $component->getRecursiveChildComponents($constraints, $childConstraints));
        }
        return $ret;
    }

    public function getChildComponents($constraints = array())
    {
        if (!is_array($constraints)) {
            if (is_string($constraints)) {
                $constraints = array('id' => $constraints);
            } else if ($constraints instanceof Zend_Db_Select) {
                $constraints = array('select' => $constraints);
            } else {
                throw new Vps_Exception("Invalid contraint");
            }
        }
        $sc = '';
        foreach ($constraints as $key => $val) {
            if ($val instanceof Zend_Db_Select) {
                $val = $val->__toString();
            }
            if (is_array($val)) {
                $val = implode('', $val);
            }
            $sc .= $key . $val;
        }
        $sc = md5($sc);
        if (!isset($this->_constraintsCache[$sc])) {
            $ret = array();

            $generatorConstraints = array();
            foreach (array('page', 'pseudoPage', 'box', 'multibox', 'generator', 'skipRoot') as $c) {
                if (isset($constraints[$c])) {
                    $generatorConstraints[$c] = $constraints[$c];
                    unset($constraints[$c]);
                }
            }
            if (isset($constraints['select']) && $constraints['select'] instanceof Vps_Db_Table_Select_Generator) {
                $generatorConstraints['generator'] = $constraints['select']->getGenerator();
            }
            if (isset($constraints['flags'])) {
                if (!is_array($constraints['flags'])) {
                    throw new Vps_Exception("Constraint 'flags' must be of type array");
                }
                if (isset($constraints['componentClass'])) {
                    throw new Vps_Exception("Constraint 'flags' may not be used with other constraints");
                }
                $constraints['componentClass'] = array();
                $classes = Vpc_Abstract::getChildComponentClasses($this->componentClass);
                foreach ($classes as $class) {
                    if ($class) {
                        if ($this->_hasFlags($class, $constraints['flags'])) {
                            $constraints['componentClass'][] = $class;
                        }
                    }
                }
                unset($constraints['flags']);
            }

            $this->_constraintsCache[$sc] = array();
            if (isset($constraints['componentClass']) && $constraints['componentClass'] == array()) {
                return $this->_constraintsCache[$sc]; //vorzeitig abbrechen, da kommt sicher kein ergebnis
            }
            $generators = $this->getGenerators($generatorConstraints);
            foreach ($generators as $generator) {
                $childConstraints = $constraints;
                if (isset($constraints['limit'])) {
                    $childConstraints['limit'] -= count($this->_constraintsCache[$sc]);
                }
                foreach ($generator->getChildData($this, $childConstraints) as $data) {
                    if (isset($this->_constraintsCache[$sc][$data->componentId])) {
                        $odata = $this->_constraintsCache[$sc][$data->componentId];
                        if (isset($data->box) && isset($odata->box) && $data->box == $odata->box) {
                            if ($data->priority > $odata->priority) {
                                unset($this->_constraintsCache[$sc][$data->componentId]);
                            } else {
                                continue;
                            }
                        }
                    }
                    if (isset($this->_constraintsCache[$sc][$data->componentId])) {
                        throw new Vps_Exception("Key for generator not unique: {$data->componentId}");
                    }
                    $this->_constraintsCache[$sc][$data->componentId] = $data;
                }
                if (isset($constraints['limit'])) {
                    if ($constraints['limit'] - count($this->_constraintsCache[$sc]) <= 0) break;
                }
            }
        }
        return $this->_constraintsCache[$sc];
    }

/*
    public function getDataFromGenerators($generator)
    {
        $ret = array();
        foreach ($generators as $generator) {
            foreach ($generator->getChildData($this, $constraints) as $data) {
                //p(get_class($generator));
                if (isset($this->_constraintsCache[$sc][$data->componentId])) {
                    throw new Vps_Exception("Key for generator not unique: {$data->componentId}");
                }
                $ret[$data->componentId] = $data;
            }
        }
        return $ret;
    }
*/
    public function getChildPages(array $constraints = array())
    {
        $constraints['page'] = true;
        return $this->getRecursiveChildComponents($constraints);
    }

    public function getChildPseudoPages(array $constraints = array())
    {
        $constraints['pseudoPage'] = true;
        return $this->getRecursiveChildComponents($constraints);
    }

    public function getChildBoxes(array $constraints = array())
    {
        $constraints['box'] = true;
        return $this->getRecursiveChildComponents($constraints);
    }

    public function getChildMultiBoxes(array $constraints = array())
    {
        $constraints['multibox'] = true;
        return $this->getRecursiveChildComponents($constraints);
    }

    private function _hasGenerator($componentClass, $interface)
    {
        static $hasGenerator = array();
        if (isset($hasGenerator[$interface][$componentClass])) {
            return $hasGenerator[$interface][$componentClass];
        }
        $hasGenerator[$interface][$componentClass] = false;

        $generators = Vpc_Abstract::getSetting($componentClass, 'generators');
        foreach ($generators as $key => $generator) {
            if (!isset($generator['class'])) {
                throw new Vps_Exception("Generator '$key' of component '$componentClass' doesn't have a class");
            }
            if (is_instance_of($generator['class'], $interface)) {
                $hasGenerator[$interface][$componentClass] = true;
                return true;
            }
            $classes = Vpc_Abstract::getChildComponentClasses($componentClass, $key);
            foreach ($classes as $class) {
                if ($class && $this->_hasGenerator($class, $interface)) {
                    $hasGenerator[$interface][$componentClass] = true;
                    return true;
                }
            }
        }
        return false;
    }

    private function _hasChildSetting($componentClass, $setting)
    {
        static $hasChildSetting = array();
        if (isset($hasChildSetting[$setting][$componentClass])) {
            return $hasChildSetting[$setting][$componentClass];
        }
        $hasChildSetting[$setting][$componentClass] = false;
        foreach (Vpc_Abstract::getChildComponentClasses($componentClass) as $class) {
            if ($class) {
                if (Vpc_Abstract::hasSetting($class, $setting)
                    && Vpc_Abstract::getSetting($class, $setting))
                {
                    $hasChildSetting[$setting][$componentClass] = true;
                    return true;
                }
                if ($this->_hasChildSetting($class, $setting)) {
                    $hasChildSetting[$setting][$componentClass] = true;
                    return true;
                }
            }
        }
        return false;
    }

    private function _hasChildFlags($componentClass, array $constraints)
    {
        static $hasChildFlags = array();
        $flags = isset($constraints['flags']) ? $constraints['flags'] : array();
        $cacheKey = serialize($flags);
        if (isset($hasChildFlags[$cacheKey][$componentClass])) {
            return $hasChildFlags[$cacheKey][$componentClass];
        }

        if ($this->_hasFlags($componentClass, $flags)) {
            $hasChildFlags[$cacheKey][$componentClass] = true;
            return true;
        } else {
            $hasChildFlags[$cacheKey][$componentClass] = false;
        }

        foreach (Vpc_Abstract::getChildComponentClasses($componentClass, $constraints) as $class) {
            if ($class) {
                if ($this->_hasChildFlags($class, $constraints)) {
                    $hasChildFlags[$cacheKey][$componentClass] = true;
                    return true;
                }
            }
        }
        return false;
    }

    protected function _hasFlags($class, array $flags)
    {
        $componentFlags = Vpc_Abstract::getSetting($class, 'flags');
        foreach ($flags as $k => $c) {
            if (!isset($componentFlags[$k])) $componentFlags[$k] = false;
            if ($componentFlags[$k] != $c) {
                return false;
            }
        }
        return true;
    }

    /**
     * Abkürzung für $this->getComponent()->hasContent()
     *
     * @return boolean $hasContent Ob die zugehörige Komponente Inhalt hat oder nicht
     */
    public function hasContent()
    {
        return $this->getComponent()->hasContent();
    }

    public function getChildPage($constraints = array())
    {
        $constraints['limit'] = 1;
        return array_shift($this->getChildPages($constraints));
    }

    public function getChildPseudoPage($constraints = array())
    {
        $constraints['limit'] = 1;
        return array_shift($this->getChildPseudoPages($constraints));
    }

    public function getGenerator($key)
    {
        return Vps_Component_Generator_Abstract::getInstance($this->componentClass, $key);
    }

    public function getChildComponentIds($constraints = array())
    {
        $ret = array();
        foreach ($this->getChildComponents($constraints) as $data) {
            $ret[] = $data->componentId;
        }
        return $ret;
    }

    public function getChildComponent($constraints = array())
    {
        if (!is_array($constraints)) {
            if (is_string($constraints)) {
                $constraints = array('id' => $constraints);
            } else if ($constraints instanceof Zend_Db_Select) {
                $constraints = array('select' => $constraints);
            } else {
                throw new Vps_Exception("Invalid contraint");
            }
        }
        $constraints['limit'] = 1;
        return array_shift($this->getChildComponents($constraints));
    }

    public function getComponent()
    {
        if (!isset($this->_component)) {
            $component = new $this->componentClass($this);
            $this->_component = $component;
        }
        return $this->_component;
    }

    public function getPage()
    {
        $page = $this;
        while ($page && !$page->isPage) {
            $page = $page->parent;
        }
        return $page;
    }

    public function getPseudoPage()
    {
        $page = $this;
        while ($page && !$page->isPseudoPage) {
            $page = $page->parent;
        }
        return $page;
    }

    public function getParentPage()
    {
        $page = $this->getPage();
        if ($page && $page->parent) {
            return $page->parent->getPage();
        }
        return null;
    }

    public function getParentPseudoPage()
    {
        $page = $this->getPseudoPage();
        if ($page && $page->parent) {
            return $page->parent->getPseudoPage();
        }
        return null;
    }

    public function getTitle()
    {
        $title = array();
        $row = $this->getPage();
        do {
            if ($row->name != '') {
                $title[] = $row->name;
            }
        } while ($row = $row->getParentPage());
        return implode(' - ', $title);
    }

    public function getPlugins($interface = null)
    {
        $ret = array();
        $plugins = Vpc_Abstract::getSetting($this->componentClass, 'plugins');
        foreach ($plugins as $p) {
            if (!$interface || is_instance_of($p, $interface)) {
                $ret[] = $p;
            }
        }
        return $ret;
    }

    public function getChildPageByPath($path)
    {
        $page = $this;
        foreach (explode('/', $path) as $pathPart) {
            $page = $page->getChildPseudoPage(array('filename' => $pathPart));
            if (!$page) break;
        }
        return $page;
    }

/*
    public function toDebug()
    {
        $ret = '';
        foreach ($this as $k=>$i) {
            if ($k == 'parent') continue;
            $ret .= "$k: $i<br >\n";
        }
        return $ret;
    }
*/
}
?>