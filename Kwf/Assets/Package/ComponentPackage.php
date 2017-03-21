<?php
class Kwf_Assets_Package_ComponentPackage extends Kwf_Assets_Package
{
    private $_packageName;
    private $_loadedPackage;
    static private $_instances = array();
    protected $_enableLegacySupport = false;

    public static function getInstance($packageName)
    {
        if (!isset(self::$_instances[$packageName])) {
            self::$_instances[$packageName] = new self($packageName, Kwf_Assets_Package_ComponentFrontend::getInstance());
        }
        return self::$_instances[$packageName];
    }

    public function __construct($packageName, $loadedPackage)
    {
        if ($packageName == 'Default') {
            throw new Kwf_Exception("Default component assets package is ment to be included in Frontend package");
        }
        if (!$packageName) {
            throw new Kwf_Exception("Required packageName");
        }
        $this->_packageName = $packageName;
        $this->_loadedPackage = $loadedPackage;
        parent::__construct(Kwf_Assets_Package_Default::getDefaultProviderList(), 'ComponentsPackage'.$packageName);
    }

    public function toUrlParameter()
    {
        if ($this->_loadedPackage != Kwf_Assets_Package_ComponentFrontend::getInstance()) {
            throw new Kwf_Exception("Only possible with ComponentFrontend as loadedPackage");
        }
        return $this->_packageName;
    }

    public static function fromUrlParameter($class, $parameter)
    {
        $param = explode(':', $parameter);
        $packageName = array_shift($param);
        $ret = self::getInstance($packageName);
        return $ret;
    }

    //remove already loaded (in _loadedPackage = Frontend) dependencies dependencies
    protected function _getFilteredUniqueDependencies($mimeType)
    {
        $ret = parent::_getFilteredUniqueDependencies($mimeType);

        $loadedDeps = $this->_loadedPackage->_getFilteredUniqueDependencies($mimeType);

        foreach ($ret as $k=>$i) {
            if (in_array($i, $loadedDeps, true)) {
                unset($ret[$k]);
            }
        }

        $ret = array_values($ret);

        return $ret;
    }

    protected function _getCacheId($mimeType)
    {
        if (get_class($this->_providerList) == 'Kwf_Assets_ProviderList_Default') { //only cache for default providerList, so cacheId doesn't have to contain only dependencyName
            return 'ComponentPackage'.str_replace(array('.'), '_', $this->_packageName).'_'.str_replace(array('/', ' ', ';', '='), '_', $mimeType);
        }
        return null;
    }

    //remove already loaded (in _loadedPackage = Frontend) commonjs dependencies
    protected function _getCommonJsData($mimeType)
    {
        $commonJsData = parent::_getCommonJsData($mimeType);
        if ($commonJsData) {
            $deps = array_merge(
                $this->_loadedPackage->_getFilteredUniqueDependencies('text/javascript'),
                $this->_loadedPackage->_getFilteredUniqueDependencies('text/javascript; defer')
            );
            foreach ($deps as $i) {
                $data = array();
                $commonJsDeps = $this->_getCommonJsDeps($i, $data);
                foreach (array_keys($data) as $key) {
                    if (isset($commonJsData[$key])) {
                        unset($commonJsData[$key]);
                    }
                }
            }
        }
        return $commonJsData;
    }

    public function getPackageContents($mimeType, $language, $includeSourceMapComment = true)
    {
        $ret = parent::getPackageContents($mimeType, $language, $includeSourceMapComment);
        if ($mimeType == 'text/javascript; defer') {
            $ret = Kwf_Assets_Package_Filter_LoadDeferred::filter($ret);
        }
        return $ret;
    }
}
