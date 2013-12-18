<?php
class Kwf_Assets_Dependency_Iterator_Recursive extends ArrayIterator implements RecursiveIterator
{
    private $_dependencyType;
    public function __construct($dependency, $dependencyType)
    {
        $this->_dependencyType = $dependencyType;
        if (!is_array($dependency)) $dependency = array($dependency);
        parent::__construct($dependency);
    }

    public function getChildren()
    {
        return new self($this->current()->getDependencies($this->_dependencyType), $this->_dependencyType);
    }

    public function hasChildren()
    {
        return (bool)count($this->current()->getDependencies($this->_dependencyType));
    }
}
