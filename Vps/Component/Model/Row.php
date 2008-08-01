<?php
class Vps_Component_Model_Row implements Vps_Model_Row_Interface 
{
    protected $_data;
    protected $_model;
    
    public function __construct(array $config)
    {
        $this->_data = $config['data'];
        $this->_model = $config['model'];
    }

    public function __isset($name)
    {
        return isset($this->_data->$name);
    }

    public function __unset($name)
    {
        unset($this->_data->$name);
    }

    public function __get($name)
    {
        if (isset($this->_data->$name)) {
            return $this->_data->$name;
        } else {
            return null;
        }
    }
        
    public function __set($name, $value)
    {
        $this->_data->$name = $value;
    }

    public function save()
    {
    }

    public function delete()
    {
    }
    
    public function getData()
    {
        return $this->_data;
    }

    public function toArray()
    {
        return array(
            'isPage' => $this->_data->isPage
        );
    }
}