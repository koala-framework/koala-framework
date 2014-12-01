<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_ProxyCache_Rowset extends Kwf_Model_Proxy_Rowset
{
    protected $_cacheData = null;
    protected $_pointer = 0;
    protected $_model;

    public function __construct($config)
    {
        if (isset($config['cacheData']))  {
            $this->_cacheData = array_values($config['cacheData']);
        }
        if (!isset($config['rowset']) && !isset($config['cacheData'])) {
            throw new Kwf_Exception("kein rowset&cacheData vorhanden");
        }
        parent::__construct($config);
    }

    public function toArray()
    {
        if (!is_null($this->_cacheData)) {
          $ret = array();
          foreach ($this as $row) {
              $ret[] = $row->toArray();
          }
          return $ret;
        } else {
            return parent::toArray();
        }
    }

    public function rewind()
    {
        if (!is_null($this->_cacheData)) {
            $this->_pointer = 0;
        } else {
            parent::rewind();
        }
        return $this;
    }

    public function current()
    {
        if (!is_null($this->_cacheData)) {
          if (!$this->valid()) {
              return null;
          }
          $cacheData = $this->_cacheData[$this->_pointer];
          if ($cacheData) {
              return $this->getModel()->getRowByCacheData($cacheData['id'], $cacheData['data']);
          }
        } else {
            return parent::current();
        }
    }

    public function key()
    {
        if (!is_null($this->_cacheData)) {
            return $this->_pointer;
        } else {
            return parent::key();
        }
    }

    public function next()
    {
        if (!is_null($this->_cacheData)) {
            ++$this->_pointer;
        } else {
            return parent::next();
        }
    }

    public function valid()
    {
        if (!is_null($this->_cacheData)) {
            return $this->_pointer < $this->count();
        } else {
            return parent::valid();
        }
    }

    public function count()
    {
        if (!is_null($this->_cacheData)) {
            return count($this->_cacheData);
        } else {
            return parent::count();
        }
    }

    public function seek($position)
    {
        if (!is_null($this->_cacheData)) {
          $position = (int) $position;
          if ($position < 0 || $position > $this->count()) {
              throw new Zend_Db_Table_Rowset_Exception("Illegal index $position");
          }
          $this->_pointer = $position;
          return $this;
        } else {
            return parrent::seek($position);
        }
    }

    public function getModel()
    {
        return $this->_model;

    }
}
