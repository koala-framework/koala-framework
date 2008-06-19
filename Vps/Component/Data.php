<?php
class Vps_Component_Data {
    
    private $_component;
    
    public function __construct($config)
    {
        foreach ($config as $k=>$i) {
            $this->$k = $i;
        }
        if (!isset($this->dbId) && isset($this->componentId)) {
            $this->dbId = $this->componentId;
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
    
    public function getUrl()
    {
        return $this->getPage()->url;
    }

    public function getFilename()
    {
        return $this->getPage()->filename;
    }

    public function getChildPages($constraints = array())
    {
        $ret = array();
        $components = $this->getChildComponents($constraints);
        foreach ($components as $component) {
            if ($component instanceof Vps_Component_Data_Page) {
                $ret[] = $component;
            } else {
                $ret = array_merge($ret, $component->getChildPages($constraints));
            }
        }
        return $ret;
    }
    
    public function getChildPage($constraints = array())
    {
        $childPages = $this->getChildPages($constraints);
        return isset($childPages[0]) ? $childPages[0] : null;
    }
    
    public function getTreeCache($class)
    {
        $tc = $this->_getTreeCache();
        if ($tc) {
            return $tc->getTreeCache($class);
        }
    }

    public function getChildComponents($constraints = array())
    {
        $ret = array();
        $tc = $this->_getTreeCache();
        if ($tc) $ret = array_merge($ret, $tc->getChildData($this, $constraints));
        return $ret;
    }
    
    protected function _getTreeCache()
    {
        return Vpc_TreeCache_Abstract::getInstance($this->componentClass);
    }
    
    public function getChildComponent($constraints = array())
    {
        if (is_string($constraints)) {
            $constraints = array('id' => $constraints);
        }
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
        while ($page && !$page instanceof Vps_Component_Data_Page) {
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
}
?>