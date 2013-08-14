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
                    $this->_dependencies[$dependencyName] = $d;
                    return $d;
                }
                $ret = new Kwf_Assets_Dependency_Dependencies(array());
                $this->_dependencies[$dependencyName] = $ret;
                foreach ($d as $i) {
                    if (is_object($i)) {
                        $ret->addDependency($i);
                    } else {
                        $d = $this->findDependency($i);
                        if (!$d) {
                            throw new Kwf_Exception("Can't find dependency '$i'");
                        }
                        $ret->addDependency($d);
                    }
                }
                return $ret;
            }
        }
        return null;
    }
}
