<?php
class Kwf_Assets_ProviderList_Abstract implements Serializable
{
    protected $_providers;
    protected $_dependencies = array();
    private $_pathTypesCache;
    protected $_pathTypesCacheId;

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

        return $providers;
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
