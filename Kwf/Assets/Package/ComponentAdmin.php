<?php
class Kwf_Assets_Package_ComponentAdmin extends Kwf_Assets_Package_Default
{
    static $_instance;
    public static function getInstance()
    {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        parent::__construct('Admin');
    }

    public function toUrlParameter()
    {
        return 'Admin';
    }

    public static function fromUrlParameter($class, $parameter)
    {
        return self::getInstance();
    }

    public function getPackageUrls($mimeType, $language)
    {
        $ret = array();

        $frontendPackage = Kwf_Assets_Package_ComponentFrontend::getInstance();
        $ret = array_merge($ret, $frontendPackage->getPackageUrls($mimeType, $language));

        $packageNames = array();
        foreach (Kwc_Abstract::getComponentClasses() as $cls) {
            if (Kwc_Abstract::getFlag($cls, 'assetsPackage')) {
                $packageName = Kwc_Abstract::getFlag($cls, 'assetsPackage');
                if ($packageName != 'Default' && !in_array($packageName, $packageNames)) {
                    $packageNames[] = $packageName;
                    $ret = array_merge($ret, Kwf_Assets_Package_ComponentPackage::getInstance($packageName)->getPackageUrls($mimeType, $language));
                }
            }
        }

        $ret = array_merge($ret, parent::getPackageUrls($mimeType, $language));
        return $ret;
    }

    protected function _getFilteredUniqueDependencies($mimeType)
    {
        $ret = parent::_getFilteredUniqueDependencies($mimeType);

        $frontendPackage = Kwf_Assets_Package_ComponentFrontend::getInstance();

        $loadedDeps = $frontendPackage->_getFilteredUniqueDependencies($mimeType);

        foreach ($ret as $k=>$i) {
            if (in_array($i, $loadedDeps, true)) {
                unset($ret[$k]);
            }
        }

        $ret = array_values($ret);

        return $ret;
    }

    protected function _getCommonJsData($mimeType)
    {
        $commonJsData = parent::_getCommonJsData($mimeType);

        $frontendPackage = Kwf_Assets_Package_ComponentFrontend::getInstance();

        if ($commonJsData) {
            $deps = array_merge(
                $frontendPackage->_getFilteredUniqueDependencies($mimeType)
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
}
