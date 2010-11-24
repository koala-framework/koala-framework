<?php
/**
 * Iterator der einen ModelIterator mit einer Paketgröße durchgeht um speicher zu sparen
 */
class Vps_Model_Iterator_Packages implements Iterator
{
    private $_packageSize;
    private $_currentOffset = 0;
    private $_innerIterator;

    public function __construct(Vps_Model_Iterator_ModelIterator_Interface $iterator, $packageSize = 500)
    {
        $this->_packageSize = $packageSize;
        $this->_innerIterator = $iterator;
    }

    public function rewind()
    {
        $s = $this->_innerIterator->getSelect();
        $s->limit($this->_packageSize, $this->_currentOffset);
        $this->_currentOffset += $this->_packageSize;
        $this->_innerIterator->rewind();
    }

    public function valid()
    {
        $ret = $this->_innerIterator->valid();
        if (!$ret) {
            $s = $this->_innerIterator->getSelect();
            $s->limit($this->_packageSize, $this->_currentOffset);
            $this->_currentOffset += $this->_packageSize;
            $this->_innerIterator->rewind();
            $ret = $this->_innerIterator->valid();
        }
        return $ret;
    }

    public function current()
    {
        return $this->_innerIterator->current();
    }

    public function key()
    {
        return $this->_innerIterator->key();
    }

    public function next()
    {
        $this->_innerIterator->next();
    }

    public function count()
    {
        return $this->_innerIterator->getModel()->countRows($this->_innerIterator->getSelect());
    }
}
