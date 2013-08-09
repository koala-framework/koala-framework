<?php
class Kwf_Assets_Dependency_Dependencies extends Kwf_Assets_Dependency_Abstract
{
    protected $_dependencies;

    public function __construct(array $dependencies)
    {
        $this->_dependencies = $dependencies;
    }

    public function getDependencies()
    {
        return $this->_dependencies;
    }
}
