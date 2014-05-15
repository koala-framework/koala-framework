<?php
class Kwf_Assets_Package_LazyLoad extends Kwf_Assets_Package
{
    protected $_loadedDependencies;

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
            $pkg = new Kwf_Assets_Package($this->_providerList, $d);

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
        if ($providerList == 'Kwf_Assets_Package_Default') {
            $providerList = Kwf_Assets_Package_Default::getDefaultProviderList();
        } else {
            $providerList = new $providerList();
        }
        $dependencyName = $params[1];
        $loadedDependencies = array();
        if ($params[2]) $loadedDependencies = explode(',', $params[2]);

        return new self($providerList, $dependencyName, $loadedDependencies);
    }
}
