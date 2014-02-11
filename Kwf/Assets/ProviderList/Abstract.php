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
        ini_set('xdebug.max_nesting_level', 200); //TODO required for ext4, find better solution for that

        if (isset($this->_dependencies[$dependencyName])) {
            return $this->_dependencies[$dependencyName];
        }
        foreach ($this->_providers as $p) {
            $d = $p->getDependency($dependencyName);
            if ($d !== null) {
                $this->_dependencies[$dependencyName] = $d;
                $this->_setDependenciesForDependency($d);
                return $d;
            }
        }
        return null;
    }

    private function _setDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        static $set = array();
        if (in_array($dependency, $set, true)) return;
        $set[] = $dependency;

        foreach ($dependency->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL) as $d) {
            $this->_setDependenciesForDependency($d);
        }

        //providers can return additional dependencies for this dependency
        $deps = $this->getDependenciesForDependency($dependency);
        foreach ($deps as $type=>$i) {
            $dependency->setDependencies($type, $i);
        }
    }

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        $ret = array();
        foreach ($this->_providers as $p) {
            $deps = $p->getDependenciesForDependency($dependency);
            foreach ($deps as $type=>$i) {
                if ($type != Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES
                    && $type != Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES
                ) {
                    throw new Kwf_Exception("invalid dependency type");
                }
                if (!is_array($i)) {
                    throw new Kwf_Exception("invalid dependency, expected array");
                }
                if (!isset($ret[$type])) $ret[$type] = array();
                $ret[$type] = array_merge($ret[$type], $i);
            }
        }
        return $ret;
    }

    public function getDefaultDependencies()
    {
        $ret = array();
        foreach ($this->_providers as $p) {
            $ret = array_merge($ret, $p->getDefaultDependencies());
        }
        return $ret;
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
