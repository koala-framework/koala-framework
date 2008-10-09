<?php
class Vps_Model_Rowset_Abstract implements Vps_Model_Rowset_Interface
{
    protected $_pointer = 0;
    protected $_count;
    protected $_rows = array();
    protected $_data;
    protected $_model;
    protected $_rowClass;

    public function __construct($config)
    {
        $this->_init();
        $this->_data = $config['data'];
        $this->_count = count($config['data']);
        $this->_model = $config['model'];
        $this->_rowClass = $config['rowClass'];
    }

    public function toArray()
    {
        return $this->_data;
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

        // do we already have a row object for this position?
        if (empty($this->_rows[$this->_pointer])) {
            $keys = array_keys($this->_data);
            $this->_rows[$this->_pointer] = new $this->_rowClass(
                array(
                    'data' => $this->_data[$keys[$this->_pointer]],
                    'model' => $this->getModel()
                )
            );
        }

        // return the row object
        return $this->_rows[$this->_pointer];
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
        return $this->_pointer < $this->_count;
    }

    public function count()
    {
        return $this->_count;
    }

    public function seek($position)
    {
        $position = (int) $position;
        if ($position < 0 || $position > $this->_count) {
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
}
