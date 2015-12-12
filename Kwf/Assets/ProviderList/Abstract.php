<?php
class Kwf_Assets_ProviderList_Abstract implements Serializable
{
    protected $_providers;
    protected $_dependencies = array();
    public function __construct(array $providers)
    {
        foreach ($providers as $p) {
            $p->setProviderList($this);
        }
        $this->_providers = $providers;
    }

    public function getProviders()
    {
        return $this->_providers;
    }

    public static function getVendorProviders()
    {
        $cacheId = 'assets-vendor-providers';
        $cachedProviders = Kwf_Cache_SimpleStatic::fetch($cacheId);
        if ($cachedProviders === false) {
            $cachedProviders = array();
            $paths = glob(VENDOR_PATH."/*/*");
            $paths[] = '.';
            foreach ($paths as $i) {
                if (is_dir($i) && file_exists($i.'/dependencies.ini')) {
                    $config = new Zend_Config_Ini($i.'/dependencies.ini');
                    if (isset($config->config)) {
                        $config = new Zend_Config_Ini($i.'/dependencies.ini', 'config');
                        if ($config->provider) {
                            $provider = $config->provider;
                            if (is_string($provider)) $provider = array($provider);
                            foreach ($provider as $p) {
                                $cachedProviders[] = array(
                                    'cls' => $p,
                                    'file' => $i.'/dependencies.ini'
                                );
                            }
                        }
                    }
                }
            }
            foreach (glob(VENDOR_PATH.'/bower_components/*') as $i) {
                $cachedProviders[] = array(
                    'cls' => 'Kwf_Assets_Provider_BowerBuiltFile',
                    'file' => $i
                );
            }
            Kwf_Cache_SimpleStatic::add($cacheId, $cachedProviders);
        }

        $providers = array();
        foreach ($cachedProviders as $p) {
            $cls = $p['cls'];
            $providers[] = new $cls($p['file']);
        }

        if (VENDOR_PATH=='../vendor') {
            $providers[] = new Kwf_Assets_Provider_Ini('../dependencies.ini');
        }

        return $providers;
    }

    /**
     * @return Kwf_Assets_Dependency_Abstract
     */
    public function findDependency($dependencyName)
    {
        ini_set('xdebug.max_nesting_level', 200); //TODO required for ext4, find better solution for that

        //here if getDependencyNameByAlias is not required for better performance
        if (isset($this->_dependencies[$dependencyName])) {
            return $this->_dependencies[$dependencyName];
        }

        foreach ($this->_providers as $p) {
            $d = $p->getDependencyNameByAlias($dependencyName);
            if (!is_null($d)) $dependencyName = $d;
        }

        if (isset($this->_dependencies[$dependencyName])) {
            return $this->_dependencies[$dependencyName];
        }
        foreach ($this->_providers as $p) {
            $d = $p->getDependency($dependencyName);
            if ($d !== null) {
                $this->_dependencies[$dependencyName] = $d;
                $this->_setDependenciesForDependency($d);
                return $d;
            }
        }
        return null;
    }

    private function _setDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        static $set = array();
        if (isset($set[spl_object_hash($dependency)])) return;
        $set[spl_object_hash($dependency)] = true;

        foreach ($dependency->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_ALL) as $d) {
            $this->_setDependenciesForDependency($d);
        }

        //providers can return additional dependencies for this dependency
        $deps = $this->getDependenciesForDependency($dependency);
        foreach ($deps as $type=>$i) {
            $dependency->addDependencies($type, $i);
        }
    }

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        $ret = array();
        foreach ($this->_providers as $p) {
            $deps = $p->getDependenciesForDependency($dependency);
            foreach ($deps as $type=>$i) {
                /*
                if ($type != Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES
                    && $type != Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES
                ) {
                    throw new Kwf_Exception("invalid dependency type");
                }
                */
                if (!is_array($i)) {
                    throw new Kwf_Exception("invalid dependency, expected array");
                }
                foreach ($i as $j) {
                    if (!$j) throw new Kwf_Exception("invalid dependency returned by '".get_class($p)."' for '$dependency'");
                }
                if (!isset($ret[$type])) $ret[$type] = array();
                $ret[$type] = array_merge($ret[$type], $i);
            }
        }
        return $ret;
    }

    public function serialize()
    {
        throw new Kwf_Exception("unsupported, should not be required");
        $ret = array();
        foreach (get_object_vars($this) as $k=>$i) {
            if ($k == '_dependencies') { //don't serialize _dependencies, that's basically just a cache
                $i = array();
            }
            $ret[$k] = $i;
        }
        return serialize($ret);
    }

    public function unserialize($serialized)
    {
        foreach (unserialize($serialized) as $k=>$i) {
            $this->$k = $i;
        }
    }
}
