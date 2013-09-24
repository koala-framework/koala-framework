<?php
class Kwf_Assets_ProviderList_Abstract implements Serializable
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
                $ret = new Kwf_Assets_Dependency_Dependencies(array(), $dependencyName);
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

    public function serialize()
    {
        $ret = array();
        foreach (get_object_vars($this) as $k=>$i) {
            if ($k == '_dependencies') { //don't serialize _dependencies, that's basically just a cache
                $i = array();
            }
            $ret[$k] = $i;
        }
        return serialize($ret);
    }

    public function unserialize($serialized)
    {
        foreach (unserialize($serialized) as $k=>$i) {
            $this->$k = $i;
        }
    }
}
