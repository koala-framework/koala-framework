<?php
abstract class Kwf_Assets_Dependency_Decorator_Abstract extends Kwf_Assets_Dependency_Abstract
{
    protected $_dep;
    public function __construct(Kwf_Assets_Dependency_Abstract $dep)
    {
        $this->_dep = $dep;
        parent::__construct();
    }

    public function usesLanguage()
    {
        return $this->_dep->usesLanguage();
    }

    public function getContentsSource()
    {
        return $this->_dep->getContentsSource();
    }

    public function getContentsSourceString()
    {
        return $this->_dep->getContentsSourceString();
    }

    public function getMimeType()
    {
        return $this->_dep->getMimeType();
    }

    public function getDeferLoad()
    {
        return $this->_dep->getDeferLoad();
    }

    public function isCommonJsEntry()
    {
        return $this->_dep->isCommonJsEntry();
    }

    public function __toString()
    {
        return str_replace('Kwf_Assets_Dependency_Decorator_', '', get_class($this)).'('.$this->_dep->__toString().')';
    }

    public function setDependencies($type, $deps)
    {
        return $this->_dep->setDependencies($type, $deps);
    }

    public function addDependencies($type, $deps)
    {
        return $this->_dep->addDependencies($type, $deps);
    }

    public function addDependency($type, $dep, $index = null)
    {
        return $this->_dep->addDependency($type, $dep, $index);
    }

    public function getDependencies($type)
    {
        return $this->_dep->getDependencies($type);
    }
}
