<?php
abstract class Vps_Controller_Action_Auto_Filter_Abstract implements Vps_Collection_Item_Interface
{
    private $_properties = array();
    protected $_defaultPropertyValues = array();
    protected $_mandatoryProperties = array();
    protected $_type = null;

    public function __construct()
    {
        $this->_init();
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'set') {
            if (!isset($arguments[0])) {
                throw new Vps_Exception("Missing argument 1 (value)");
            }
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->setProperty($name, $arguments[0]);
        } else if (substr($method, 0, 3) == 'get') {
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->getProperty($name);
        } else {
            throw new Vps_Exception("Invalid method called: '$method'");
        }
    }

    public function setProperty($name, $value)
    {
        $this->_properties[$name] = $value;
        return $this;
    }

    public function getProperty($name, $ignoreMandatory = false)
    {
        if (isset($this->_properties[$name])) {
            return $this->_properties[$name];
        } else if (isset($this->_defaultPropertyValues[$name])) {
            return $this->_defaultPropertyValues[$name];
        } else if (!$ignoreMandatory && in_array($name, $this->_mandatoryProperties)) {
            throw new Vps_Exception("Parameter '$name' has to be set for Filter " . get_class($this));
        } else {
            return null;
        }
    }

    protected function _init() {}

    public abstract function formatSelect($select, $query = array());

    public function getExtConfig()
    {
        if (!$this->_type) {
            throw new Vps_Exception("property '_type' must be set.");
        }

        $ret = $this->_properties;
        $ret['type'] = $this->_type;
        $ret['name'] = $this->getName();
        $ret['paramName'] = $this->getParamName();
        foreach ($ret as $key => $val) {
            if ($key == 'icon' && is_object($val)) {
                $ret[$key] = $val->__toString();
            } else if (is_object($val)) {
                unset($ret[$key]);
            }
        }
        return $ret;
    }

    public function getParamName()
    {
        return 'query_' . $this->getName();
    }

    public function hasChildren() {
        return false;
    }

    public function getChildren() {
        return array();
    }

    public function getByName($name)
    {
        if ($this->getName() == $name) return $this;
        return null;
    }

    public function getName()
    {
        return '';
    }
}
