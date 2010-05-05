<?php
class Vpc_Menu_Abstract_MenuRow extends Vps_Model_Row_Abstract
{
    private $_data;

    public function __construct(array $config)
    {
        $this->_data = $config['data'];
        parent::__construct($config);
    }

    public function __isset($name)
    {
        return isset($this->_data->$name);
    }

    public function __get($name)
    {
        return $this->_data->$name;
    }

    public function __set($name, $value)
    {
    }

    public function getData()
    {
        return $this->_data;
    }
}