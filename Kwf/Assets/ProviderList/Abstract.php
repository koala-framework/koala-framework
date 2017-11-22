<?php
class Kwf_Assets_ProviderList_Abstract implements Serializable
{
    protected $_providers;
    protected $_filters;
    protected $_dependencies = array();
    private $_dependencyIdentifiers = array();
    private $_pathTypesCache;
    protected $_pathTypesCacheId;

    public function __construct(array $providers, array $filters)
    {
        foreach ($providers as $p) {
            $p->setProviderList($this);
        }
        $this->_providers = $providers;
        $this->_filters = $filters;
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
            foreach (glob('node_modules/*') as $i) {
                if (substr($i, strlen('node_modules/'), 1) === '@') continue;

                $cachedProviders[] = array(
                    'cls' => 'Kwf_Assets_Provider_Npm',
                    'file' => $i
                );
            }
            foreach (glob('node_modules/@*/*') as $i) {
                $cachedProviders[] = array(
                    'cls' => 'Kwf_Assets_Provider_Npm',
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
                $id = $d->getIdentifier();
                if (isset($this->_dependencyIdentifiers[$id])) {
                    throw new Kwf_Exception("Dependency '$d' uses an non-unique identifier '$id'");
                }
                $this->_dependencyIdentifiers[$id] = true;
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

    public function getFilters()
    {
        return $this->_filters;
    }

    public function getPathTypes()
    {
        if (isset($this->_pathTypesCache)) return $this->_pathTypesCache;
        if (isset($this->_pathTypesCacheId)) {
            $ret = Kwf_Cache_SimpleStatic::fetch($this->_pathTypesCacheId);
            if ($ret !== false) {
                $this->_pathTypesCache = $ret;
                return $ret;
            }
        }

        $ret = array(
            'webComponents' => 'components',
            'webThemes' => 'themes',
        );
        $vendors[] = KWF_PATH; //required for kwf tests, in web kwf is twice in $vendors but that's not a problem
        $vendors[] = '.';
        $vendors = array_merge($vendors, glob(VENDOR_PATH."/*/*"));
        foreach ($vendors as $i) {
            if (is_dir($i) && file_exists($i.'/dependencies.ini')) {
                $c = new Zend_Config_Ini($i.'/dependencies.ini');
                if ($c->config) {
                    $dep = new Zend_Config_Ini($i.'/dependencies.ini', 'config');
                    $pathType = (string)$dep->pathType;
                    if ($pathType) {
                        $ret[$pathType] = $i;
                    }
                }
            }
        }

        foreach (array_reverse($this->_providers) as $p) {
            $ret = array_merge($ret, $p->getPathTypes());
        }

        $ret['web'] = '.';


        Kwf_Cache_SimpleStatic::add($this->_pathTypesCacheId, $ret);
        $this->_pathTypesCache = $ret;
        return $ret;
    }
}
