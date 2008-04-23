<?php
class Vps_Model_Db_Row implements Vps_Model_Row_Interface
{
    protected $_rowClass = 'Vps_Model_Row_Abstract';
    protected $_rowsetClass = 'Vps_Model_Rowset_Abstract';
    protected $_row;
    protected $_model;

    public function __construct(array $config)
    {
        $this->_row = $config['row'];
        $this->_model = $config['model'];
    }

    public function __isset($name)
    {
        return isset($this->_row->$name);
    }

    public function __unset($name)
    {
        unset($this->_row->$name);
    }

    public function __get($name)
    {
        return $this->_row->$name;
    }

    public function __set($name, $value)
    {
        $this->_row->$name = $value;
    }

    public function save()
    {
        $this->_row->save();
    }

    public function delete()
    {
        $this->_row->delete();
    }

    public function toDebug()
    {
        return $this->_row->toDebug();
    }
    public function __toString()
    {
        return $this->_row->__toString();
    }

    public function getRow()
    {
        return $this->_row;
    }
    public function getModel()
    {
        return $this->_model;
    }
}
