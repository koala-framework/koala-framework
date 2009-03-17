<?php
class Vps_Model_Rowset_Abstract implements Vps_Model_Rowset_Interface
{
    protected $_pointer = 0;
    protected $_dataKeys;
    protected $_model;

    public function __construct($config)
    {
        $this->_init();
        $this->_dataKeys = $config['dataKeys'];
        $this->_model = $config['model'];
    }

    public function toArray()
    {
        $ret = array();
        foreach ($this as $row) {
            $ret[] = $row->toArray();
        }
        return $ret;
    }

    protected function _init()
    {
    }

    public function rewind()
    {
        $this->_pointer = 0;
        return $this;
    }

    public function current()
    {
        if ($this->valid() === false) {
            return null;
        }
        $key = $this->_dataKeys[$this->_pointer];
        return $this->getModel()->getRowByDataKey($key);
    }

    public function key()
    {
        return $this->_pointer;
    }

    public function next()
    {
        ++$this->_pointer;
    }

    public function valid()
    {
        return $this->_pointer < $this->count();
    }

    public function count()
    {
        return count($this->_dataKeys);
    }

    public function seek($position)
    {
        $position = (int) $position;
        if ($position < 0 || $position > $this->count()) {
            require_once 'Zend/Db/Table/Rowset/Exception.php';
            throw new Zend_Db_Table_Rowset_Exception("Illegal index $position");
        }
        $this->_pointer = $position;
        return $this;
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function getTable()
    {
        return $this->getModel()->getTable();
    }
}
