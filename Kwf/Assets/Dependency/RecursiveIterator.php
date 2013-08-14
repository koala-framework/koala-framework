<?php
class Kwf_Assets_Dependency_RecursiveIterator extends ArrayIterator implements RecursiveIterator
{
    public function __construct(Kwf_Assets_Dependency_Abstract $dependency)
    {
        parent::__construct($dependency->getDependencies());
    }

    public function getChildren()
    {
        return new self($this->current());
    }

    public function hasChildren()
    {
        return (bool)count($this->current()->getDependencies());
    }
}
