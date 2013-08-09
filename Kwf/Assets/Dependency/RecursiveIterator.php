<?php
class Kwf_Assets_Dependency_RecursiveIterator extends ArrayIterator implements RecursiveIterator
{
    protected $_position;
    protected $_dependencies;

    public function __construct(Kwf_Assets_Dependency_Abstract $dependency)
    {
        $this->_position = 0;
        $this->_dependencies = $dependency->getDependencies();
    }

    public function current()
    {
        return $this->_dependencies[$this->_position];
    }

    public function getChildren()
    {
        return new self($this->current());
    }

    public function hasChildren()
    {
        return (bool)count($this->current()->getDependencies());
    }

    public function key()
    {
        return $this->_position;
    }

    public function next()
    {
        $this->_position++;
    }

    public function rewind()
    {
        $this->_position = 0;
    }

    public function valid()
    {
        return $this->_position < count($this->_dependencies);
    }
}
