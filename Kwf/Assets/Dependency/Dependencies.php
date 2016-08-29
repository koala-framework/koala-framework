<?php
class Kwf_Assets_Dependency_Dependencies extends Kwf_Assets_Dependency_Abstract
{
    protected $_name;

    public function __construct(Kwf_Assets_ProviderList_Abstract $providerList, array $dependencies, $name = null)
    {
        $this->setDependencies(self::DEPENDENCY_TYPE_REQUIRES, $dependencies);
        $this->_name = $name;
        parent::__construct($providerList);
    }

    public function __toString()
    {
        if ($this->_name) return $this->_name;
        return parent::__toString();
    }

    public function getIdentifier()
    {
        if (!$this->_name) throw new Kwf_Exception('No name set for depdency');
        return $this->_name;
    }

    public function addDependency($type, Kwf_Assets_Dependency_Abstract $dependency, $index = null)
    {
        if (!is_null($index)) {
            $this->_dependencies[$type][$index] = $dependency;
        } else {
            $this->_dependencies[$type][] = $dependency;
        }
        return $this;
    }

    public function getContentsPacked()
    {
        return null;
    }

    public function getContentsSource()
    {
        return null;
    }

    public function getContentsSourceString()
    {
        return null;
    }
}
