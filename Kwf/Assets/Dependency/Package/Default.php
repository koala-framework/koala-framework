<?php
class Kwf_Assets_Dependency_Package_Default extends Kwf_Assets_Dependency_Package
{
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

    public function __construct($dependencyName)
    {
        parent::__construct(new Kwf_Assets_ProviderList_Default(), $dependencyName);
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
}
