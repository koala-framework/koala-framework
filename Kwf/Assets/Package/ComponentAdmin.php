<?php
class Kwf_Assets_Package_ComponentAdmin extends Kwf_Assets_Package_Default
{
    static $_instance;
    public static function getInstance($dependencyName = null)
    {
        if ($dependencyName) throw new Kwf_Exception("Parameter must be null");
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        parent::__construct('AdminMain'); //Admin + Main to start ext2 application
    }

    public function toUrlParameter()
    {
        return 'Admin';
    }

    public static function fromUrlParameter($class, $parameter)
    {
        return self::getInstance();
    }

    private function _getFrontendPackages()
    {
        $ret = array();

        $ret[] = Kwf_Assets_Package_ComponentFrontend::getInstance();

        $packageNames = array();
        foreach (Kwc_Abstract::getComponentClasses() as $cls) {
            if (Kwc_Abstract::getFlag($cls, 'assetsPackage')) {
                $packageName = Kwc_Abstract::getFlag($cls, 'assetsPackage');
                if ($packageName != 'Default' && !in_array($packageName, $packageNames)) {
                    $packageNames[] = $packageName;
                    $ret[] = Kwf_Assets_Package_ComponentPackage::getInstance($packageName);
                }
            }
        }
        return $ret;
    }

    protected function _buildPackageUrls($mimeType, $language)
    {
        $ret = array();

        foreach ($this->_getFrontendPackages() as $package) {
            $ret = array_merge($ret, $package->_buildPackageUrls($mimeType, $language));
        }

        $ret = array_merge($ret, parent::_buildPackageUrls($mimeType, $language));

        return $ret;
    }

    protected function _getFilteredUniqueDependencies($mimeType)
    {
        if ($mimeType == 'text/javascript') {
            return array();
        }


        if ($mimeType == 'text/javascript; defer') {
            $ret = parent::_getFilteredUniqueDependencies('text/javascript');
            foreach (parent::_getFilteredUniqueDependencies('text/javascript; defer') as $i) {
                if (!in_array($i, $ret, true)) {
                    $ret[] = $i;
                }
            }
        } else {
            $ret = parent::_getFilteredUniqueDependencies($mimeType);
        }

        $loadedDeps = array();
        foreach ($this->_getFrontendPackages() as $package) {
            if ($mimeType == 'text/javascript; defer') {
                $loadedDeps = array_merge($loadedDeps, $package->_getFilteredUniqueDependencies('text/javascript'));
            }
            $loadedDeps = array_merge($loadedDeps, $package->_getFilteredUniqueDependencies($mimeType));
        }

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
        if ($mimeType == 'text/javascript') {
            return array();
        }



        if ($mimeType == 'text/javascript; defer') {
            $commonJsData = parent::_getCommonJsData('text/javascript');
            foreach (parent::_getCommonJsData('text/javascript; defer') as $k=>$i) {
                if (!isset($commonJsData[$k])) {
                    $commonJsData[$k] = $i;
                }
            }
        } else {
            $commonJsData = parent::_getCommonJsData($mimeType);
        }

        if ($commonJsData) {
            $deps = array();
            foreach ($this->_getFrontendPackages() as $package) {
                if ($mimeType == 'text/javascript; defer') {
                    $deps = array_merge($deps, $package->_getFilteredUniqueDependencies('text/javascript'));
                }
                $deps = array_merge($deps, $package->_getFilteredUniqueDependencies($mimeType));
            }
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
