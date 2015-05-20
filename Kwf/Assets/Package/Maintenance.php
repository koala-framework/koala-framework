<?php
class Kwf_Assets_Package_Maintenance extends Kwf_Assets_Package implements Kwf_Assets_Package_FactoryInterface
{
    private static $_defaultProviderList;

    static private $_instances = array();

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
            self::$_defaultProviderList = new Kwf_Assets_ProviderList_Maintenance();
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

    protected function _getCacheId($mimeType)
    {
        if (get_class($this->_providerList) == 'Kwf_Assets_ProviderList_Maintenance') { //only cache for default providerList, so cacheId doesn't have to contain only dependencyName
            return 'maint_'.str_replace(array('.'), '_', $this->_dependencyName).'_'.str_replace(array('/', ' ', ';', '='), '_', $mimeType);
        }
        return null;
    }

    public static function createPackages()
    {
        return array(
            self::getInstance('Maintenance')
        );
    }
}
