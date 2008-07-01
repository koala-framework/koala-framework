<?php
class Vps_Model_Row_Abstract implements Vps_Model_Row_Interface
{
    protected $_data;
    protected $_model;
    private $_internalId;
    
    public function __construct(array $config)
    {
        if (isset($config['data'])) {
            $this->_data = (array)$config['data'];
        } else {
            $this->_data = array();
        }
        $this->_model = $config['model'];
        static $internalId = 0;
        $this->_internalId = $internalId++;
    }

    public function getInternalId()
    {
        return $this->_internalId;
    }

    public function __isset($name)
    {
        return isset($this->_data[$name]);
    }

    public function __unset($name)
    {
        unset($this->_data[$name]);
    }

    public function __get($name)
    {
        if (isset($this->_data[$name])) {
            return $this->_data[$name];
        } else {
            return null;
        }
    }

    public function __set($name, $value)
    {
        $this->_data[$name] = $value;
    }

    public function save()
    {
    }

    public function delete()
    {
    }
    public function getModel()
    {
        return $this->_model;
    }

    protected function _getPrimaryKey()
    {
        return $this->_model->getPrimaryKey();
    }
    public function toArray()
    {
        return $this->_data;
    }

}

