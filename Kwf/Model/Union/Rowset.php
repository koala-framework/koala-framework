<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_Union_Rowset implements Kwf_Model_Rowset_Interface
{
    protected $_rowClass;
    protected $_ids;
    protected $_model;

    public function __construct(array $config)
    {
        if (isset($config['ids'])) $this->_ids = new ArrayObject($config['ids']);
        $this->_it = $this->_ids->getIterator();
        $this->_rowClass = $config['rowClass'];
        $this->_model = $config['model'];
    }

    public function rewind()
    {
        $this->_it->rewind();
        return $this;
    }

    public function current()
    {
        return $this->_model->_getRowById($this->_it->current());
    }

    public function key()
    {
        return $this->_it->key();
    }

    public function next()
    {
        $this->_it->next();
    }

    public function valid()
    {
        return $this->_it->valid();
    }

    public function count()
    {
        return $this->_it->count();
    }
    public function seek($position)
    {
        $this->_it->seek($position);
        return $this;
    }

    public function offsetExists($offset)
    {
        return $this->_it->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->_model->_getRowById($this->_it->offsetGet($offset));
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
    }

    public function getModel()
    {
        return $this->_model;
    }

    public function toArray()
    {
        $ret = array();
        foreach ($this as $i) {
            $ret[] = $i->toArray();
        }
        return $ret;
    }

    public function toDebug()
    {
        $i = get_class($this).': '.count($this).' rows';
        $ret = print_r($this->toArray(), true);
        $ret = preg_replace('#^Array#', $i, $ret);
        $ret .= "Model: ".get_class($this->getModel());
        $ret = "<pre>$ret</pre>";
        return $ret;
    }
}
