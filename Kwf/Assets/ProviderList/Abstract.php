<?php
class Kwf_Assets_ProviderList_Abstract
{
    protected $_providers;
    protected $_dependencies = array();
    public function __construct(array $providers)
    {
        $this->_providers = $providers;
    }

    /**
     * @return Kwf_Assets_Dependency_Abstract
     */
    public function findDependency($dependencyName)
    {
        if (isset($this->_dependencies[$dependencyName])) {
            return $this->_dependencies[$dependencyName];
        }
        foreach ($this->_providers as $p) {
            $d = $p->getDependency($dependencyName);
            if ($d !== null) {
                if (is_object($d)) {
                    return $d;
                }
                $dependencies = array();
                foreach ($d as $i) {
                    if (is_object($i)) {
                        $dependencies[] = $i;
                    } else {
                        $dependencies[] = $this->findDependency($i);
                    }
                }
                $this->_dependencies[$dependencyName] = new Kwf_Assets_Dependency_Dependencies($dependencies);
                return $this->_dependencies[$dependencyName];
            }
        }
        return null;
    }
}
