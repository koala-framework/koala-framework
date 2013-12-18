<?php
class Kwf_Assets_Dependency_Dependencies extends Kwf_Assets_Dependency_Abstract
{
    protected $_name;

    public function __construct(array $dependencies, $name = null)
    {
        $this->setDependencies(self::DEPENDENCY_TYPE_REQUIRES, $dependencies);
        $this->_name = $name;
    }

    public function __toString()
    {
        if ($this->_name) return $this->_name;
        return parent::__toString();
    }
}
