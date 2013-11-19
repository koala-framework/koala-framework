<?php
class Kwf_Assets_ProviderList_Abstract implements Serializable
{
    protected $_providers;
    protected $_dependencies = array();
    public function __construct(array $providers)
    {
        foreach ($providers as $p) {
            $p->setProviderList($this);
        }
        $this->_providers = $providers;
    }

    public function getProviders()
    {
        return $this->_providers;
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
                $this->_dependencies[$dependencyName] = $d;
                return $d;
            }
        }
        return null;
    }

    public function serialize()
    {
        throw new Kwf_Exception("unsupported, should not be required");
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
