<?php
class Vps_Model_Db_Row implements Vps_Model_Row_Interface
{
    protected $_rowClass = 'Vps_Model_Row_Abstract';
    protected $_rowsetClass = 'Vps_Model_Rowset_Abstract';
    protected $_row;
    protected $_model;
    private $_internalId;

    public function __construct(array $config)
    {
        $this->_row = $config['row'];
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
        return isset($this->_row->$name);
    }

    public function __unset($name)
    {
        unset($this->_row->$name);
    }

    public function __get($name)
    {
        $value = $this->_row->$name;
        if (is_string($value) && substr($value, 0, 13) =='vpsSerialized') {
            $value = unserialize(substr($value, 13));
        }
        return $value;
    }

    public function __set($name, $value)
    {
        if (is_array($value) || is_object($value)) {
            $value = 'vpsSerialized'.serialize($value);
        }
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
    public function toArray()
    {
        return $this->_row->toArray();
    }
}
