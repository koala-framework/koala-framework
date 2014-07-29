<?php
class Kwf_Assets_Package_LazyLoad extends Kwf_Assets_Package
{
    protected $_loadedDependencies;
    static private $_instances = array();

    /**
     * Returns a Default Asset Package (using Kwf_Assets_ProviderList_Default)
     *
     * Very fast, as all expensive operations are done lazily
     */
    public static function getInstance($dependencyName, array $loadedDependencies)
    {
        $k = $dependencyName . '_' . implode('_', $loadedDependencies);
        if (!isset(self::$_instances[$k])) {
            self::$_instances[$k] = new self(Kwf_Assets_Package_Default::getDefaultProviderList(), $dependencyName, $loadedDependencies);
        }
        return self::$_instances[$k];
    }

    public function __construct($providerList, $dependencyName, array $loadedDependencies)
    {
        $this->_loadedDependencies = $loadedDependencies;
        parent::__construct($providerList, $dependencyName);
    }

    protected function _getFilteredUniqueDependencies($mimeType)
    {
        $ret = parent::_getFilteredUniqueDependencies($mimeType);

        $loadedDeps = array();
        foreach ($this->_loadedDependencies as $d) {
            if ($this->_providerList === Kwf_Assets_Package_Default::getDefaultProviderList()) {
                $pkg = Kwf_Assets_Package_Default::getInstance($d);
            } else {
                $pkg = new Kwf_Assets_Package($this->_providerList, $d);
            }

            $loadedDeps = array_merge($loadedDeps, $pkg->_getFilteredUniqueDependencies($mimeType));
        }

        foreach ($ret as $k=>$i) {
            if (in_array($i, $loadedDeps, true)) {
                unset($ret[$k]);
            }
        }

        $ret = array_values($ret);

        return $ret;
    }

    public function toUrlParameter()
    {
        return get_class($this->_providerList).':'.$this->_dependencyName.':'.implode(',', $this->_loadedDependencies);
    }

    public static function fromUrlParameter($class, $parameter)
    {
        $params = explode(':', $parameter);
        $providerList = $params[0];
        $dependencyName = $params[1];
        $loadedDependencies = array();
        if ($params[2]) $loadedDependencies = explode(',', $params[2]);
        if ($providerList == 'Kwf_Assets_ProviderList_Default') {
            return self::getInstance($dependencyName, $loadedDependencies);
        } else {
            $providerList = new $providerList();
            return new self($providerList, $dependencyName, $loadedDependencies);
        }
    }

    protected function _getCacheId($mimeType)
    {
        if (get_class($this->_providerList) == 'Kwf_Assets_ProviderList_Default') { //only cache for this class, so cacheId doesn't have to contain only dependencyName
            return str_replace(array('.'), '_', $this->_dependencyName.implode('_', $this->_loadedDependencies))
                .'_'.str_replace(array('/', ' ', ';', '='), '_', $mimeType);
        }
        return null;
    }
}
