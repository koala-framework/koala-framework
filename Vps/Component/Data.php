<?php
class Vps_Component_Data
{
    private $_component;

    private $_url;
    private $_rel;
    private $_filename;
    private $_componentIdCache = array();
    private $_constraintsCache = array();
    
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

        Vps_Benchmark::count('componentDatas');
    }

    public function __get($var)
    {
        if ($var == 'url') {
            $filenames = array();
            $page = $this->getPage();
            do {
                $filenames[] = $page->filename;
            } while ($page = $page->getParentPage());
            return '/'.implode('/', array_reverse($filenames));
        } else if ($var == 'rel') {
            return $this->getPage()->_rel;
        } else if ($var == 'filename') {
            return $this->getPage()->_filename;
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
    
    private function _getRecursiveChildComponents($constraints)
    {
        $childConstraints = array('page'=>false);
        $childConstraints['componentClass'] = array();
        if (isset($constraints['page']) && $constraints['page']) {
            $generatorInterface = 'Vps_Component_Generator_Page_Interface';
        } else if (isset($constraints['box']) && $constraints['box']) {
            $generatorInterface = 'Vps_Component_Generator_Box_Interface';
        } else {
            $generatorInterface = false;
        }
        if ($generatorInterface) {
            $classes = Vpc_Abstract::getChildComponentClasses($this->componentClass);
            foreach ($classes as $class) {
                if ($this->_hasGenerator($class, $generatorInterface)) {
                    $childConstraints['componentClass'][] = $class;
                }
            }
        }
        
        $ret = $this->getChildComponents($constraints);
        foreach ($this->getChildComponents($childConstraints) as $component) {
            $ret = array_merge($ret, $component->_getRecursiveChildComponents($constraints));
        }
        return $ret;
    }

    public function getChildPages($constraints = array())
    {
        $constraints['page'] = true;
        return $this->_getRecursiveChildComponents($constraints);
    }
    public function getChildBoxes($constraints = array())
    {
        $constraints['box'] = true;
        return $this->_getRecursiveChildComponents($constraints);
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
        $childPages = $this->getChildPages($constraints);
        return isset($childPages[0]) ? $childPages[0] : null;
    }

    public function getGenerator($key)
    {
        return Vps_Component_Generator_Abstract::getInstance($this->componentClass, $key);
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
            foreach (array('page', 'box', 'generator') as $c) {
                if (isset($constraints[$c])) {
                    $generatorConstraints[$c] = $constraints[$c];
                    unset($constraints[$c]);
                }
            }
            if (isset($constraints['select']) && $constraints['select'] instanceof Vps_Db_Table_Select_Generator) {
                $generatorConstraints['generator'] = $constraints['select']->getGenerator();
            }
            $generators = Vps_Component_Generator_Abstract::getInstances($this->componentClass, $this, $generatorConstraints);
            $this->_constraintsCache[$sc] = array();
            foreach ($generators as $generator) {
                $this->_constraintsCache[$sc] = array_merge($this->_constraintsCache[$sc], $generator->getChildData($this, $constraints));
            }
            $ids = array();
            foreach ($this->_constraintsCache[$sc] as $data) {
                if (in_array($data->componentId, $ids)) {
                    throw new Vps_Exception("Key für generator not unique: {$data->componentId}");
                }
                $ids[] = $data->componentId;
            }
        }
        return $this->_constraintsCache[$sc];

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
        $childComponents = $this->getChildComponents($constraints);
        return isset($childComponents[0]) ? $childComponents[0] : null;
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
    
    public function getParentPage()
    {
        $parent = $this->getPage()->parent;
        if ($parent) {
            return $parent->getPage();
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