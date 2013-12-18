<?php
class Kwf_Assets_Package_Default extends Kwf_Assets_Package
{
    private static $_defaultProviderList;
    static private $_instances = array();
    /**
     * Returns a Default Asset Package (using Kwf_Assets_ProviderList_Default)
     *
     * Very fast, as all expensive operations are done lazily
     */
    public static function getInstance($dependencyName)
    {
        if (!isset(self::$_instances[$dependencyName])) {
            self::$_instances[$dependencyName] = new self($dependencyName);
        }
        return self::$_instances[$dependencyName];
    }

    public function clearInstances()
    {
        self::$_instances = array();
    }

    public static function getDefaultProviderList()
    {
        if (!isset(self::$_defaultProviderList)) {
            self::$_defaultProviderList = new Kwf_Assets_ProviderList_Default();
        }
        return self::$_defaultProviderList;
    }

    public function __construct($dependencyName)
    {
        parent::__construct(self::getDefaultProviderList(), $dependencyName);
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
