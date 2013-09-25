<?php
class Kwf_Assets_Dependency_Package_Default extends Kwf_Assets_Dependency_Package
{
    private static $_defaultProviderList;
    /**
     * Returns a Default Asset Package (using Kwf_Assets_ProviderList_Default)
     *
     * Very fast, as the result is cached
     */
    public static function getInstance($dependencyName)
    {
        static $instances = array();
        if (isset($instances[$dependencyName])) return $instances[$dependencyName];

        $cacheId = 'depPkgDef-'.$dependencyName;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId);
        if (!$ret) {
            $ret = new self($dependencyName);
            Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        }
        $instances[$dependencyName] = $ret;
        return $ret;
    }

    private static function _getDefaultProviderList()
    {
        if (!isset(self::$_defaultProviderList)) {
            self::$_defaultProviderList = new Kwf_Assets_ProviderList_Default();
        }
        return self::$_defaultProviderList;
    }

    public function __construct($dependencyName)
    {
        parent::__construct(self::_getDefaultProviderList(), $dependencyName);
    }

    public function toUrlParameter()
    {
        return $this->_dependencyName;
    }

    public static function fromUrlParameter($class, $parameter)
    {
        $dependencyName = $parameter;
        return self::getInstance($dependencyName);
    }

    public function serialize()
    {
        $pl = $this->_providerList;
        unset($this->_providerList); //don't serialize _providerList, will be set to default on unserialize
        $ret = parent::serialize();
        $this->_providerList = $pl;
        return $ret;
    }

    public function unserialize($serialized)
    {
        parent::unserialize($serialized);
        $this->_providerList = self::_getDefaultProviderList();
    }
}
