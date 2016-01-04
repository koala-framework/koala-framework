<?php
class Kwf_Assets_Package_Default extends Kwf_Assets_Package implements Kwf_Assets_Package_FactoryInterface
{
    private static $_defaultProviderList;
    static private $_instances = array();
    /**
     * Returns a Default Asset Package (using Kwf_Assets_ProviderList_Default)
     *
     * Very fast, as all expensive operations are done lazily
     *
     * @return self
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
        return $this->_dependencyName.($this->_enableLegacySupport ? ':l' : '');
    }

    public static function fromUrlParameter($class, $parameter)
    {
        $param = explode(':', $parameter);
        $dependencyName = $param[0];
        $ret = self::getInstance($dependencyName);
        if (isset($param[1]) && $param[1] == 'l') {
            $ret->setEnableLegacySupport(true);
        }
        return $ret;

    }

    protected function _getCacheId($mimeType)
    {
        if (get_class($this->_providerList) == 'Kwf_Assets_ProviderList_Default') { //only cache for default providerList, so cacheId doesn't have to contain only dependencyName
            return str_replace(array('.'), '_', $this->_dependencyName).'_'.str_replace(array('/', ' ', ';', '='), '_', $mimeType);
        }
        return null;
    }

    public static function createPackages()
    {
        $packages = array();
        $packages[] = self::getInstance('Frontend')
            ->setEnableLegacySupport(true);
        $packages[] = self::getInstance('Admin');
        foreach (Kwf_Config::getValueArray('assets.packages') as $i) {
            $packages[] = self::getInstance($i);
        }
        return $packages;
    }

}
