<?php
class Kwf_Assets_Dependency_Package_Default extends Kwf_Assets_Dependency_Package
{
    private static $_defaultProviderList;
    static private $_instances = array();
    /**
     * Returns a Default Asset Package (using Kwf_Assets_ProviderList_Default)
     *
     * Very fast, as the result is cached
     */
    public static function getInstance($dependencyName)
    {
        if (isset(self::$_instances[$dependencyName])) return self::$_instances[$dependencyName];

        $cacheId = 'depPkgDef_'.$dependencyName;
        $ret = Kwf_Assets_Cache::getInstance()->load($cacheId);
        if (!$ret) {
            $ret = new self($dependencyName);
            Kwf_Assets_Cache::getInstance()->save($ret, $cacheId);
        }
        self::$_instances[$dependencyName] = $ret;
        return $ret;
    }

    public function clearInstances()
    {
        self::$_instances = array();
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
