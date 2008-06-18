<?php
class Vps_Component_Data {
    
    protected $_config;
    private $_component;
    
    public function __construct($config)
    {
        $this->_config = $config;
    }
    
    public function __get($key)
    {
        if (isset($this->_config[$key])) {
            return $this->_config[$key];
        }
        if ($key == 'dbId') {
            return $this->componentId;
        }
        throw new Vps_Exception('Config-Parameter ' . $key . ' not set in Vps_Component_Data');
    }
    
    public function getChildPages($constraints)
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
    
    public function getChildPage($constraints)
    {
        $childPages = $this->getChildPages($constraints);
        return isset($childPages[0]) ? $childPages[0] : null;
    }

    public function getChildComponents($constraints)
    {
        $ret = array();
        $tc = Vpc_TreeCache_Abstract::getInstance($this->getComponentClass());
        if ($tc) $ret = array_merge($ret, $tc->getChildData($this, $constraints));
        return $ret;
    }
    
    public function getChildComponent($constraints)
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
}
?>