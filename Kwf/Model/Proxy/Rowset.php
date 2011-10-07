<?php
class Vps_Model_Proxy_Rowset implements Vps_Model_Rowset_Interface
{
    protected $_rowClass;
    protected $_rowset;
    protected $_model;

    public function __construct(array $config)
    {
        if (isset($config['rowset'])) $this->_rowset = $config['rowset'];
        $this->_rowClass = $config['rowClass'];
        $this->_model = $config['model'];
    }

    public function rewind()
    {
        $this->_rowset->rewind();
        return $this;
    }

    public function current()
    {
        $row = $this->_rowset->current();
        if (is_null($row)) return null;
        return $this->_model->getRowByProxiedRow($row);
    }

    public function key()
    {
        return $this->_rowset->key();
    }

    public function next()
    {
        $this->_rowset->next();
    }

    public function valid()
    {
        return $this->_rowset->valid();
    }

    public function count()
    {
        return $this->_rowset->count();
    }
    public function seek($position)
    {
        $this->_rowset->seek($position);
        return $this;
    }
    public function getRowset()
    {
        return $this->_rowset;
    }
    public function getModel()
    {
        return $this->_model;
    }
    public function toArray()
    {
        return $this->_rowset->toArray();
    }
    public function getTable()
    {
        return $this->getModel()->getTable();
    }
}
