<?php
class Kwf_Assets_Package
    implements Kwf_Assets_Interface_UrlResolvable, Serializable
{
    protected $_providerList;
    protected $_dependencyName;
    protected $_dependency;
    protected $_cacheFilteredUniqueDependencies;

    public function __construct(Kwf_Assets_ProviderList_Abstract $providerList, $dependencyName)
    {
        $this->_providerList = $providerList;
        if (!is_string($dependencyName)) {
            throw new Kwf_Exception("dependencyName needs to be a string");
        }
        $this->_dependencyName = $dependencyName;
    }

    public function getDependencyName()
    {
        return $this->_dependencyName;
    }

    public function getDependency()
    {
        if (!isset($this->_dependency)) {
            $d = $this->_providerList->findDependency($this->_dependencyName);
            if (!$d) {
                throw new Kwf_Exception("Could not resolve dependency '$this->_dependencyName'");
            }
            $this->_dependency = $d;
        }
        return $this->_dependency;
    }

    public function getMaxMTimeCacheId($mimeType)
    {
        $ret = $this->_getCacheId($mimeType);
        if (!$ret) return $ret;
        return 'mtime_'.$ret;
    }

    //default impl doesn't cache, overriden in Package_Default
    protected function _getCacheId($mimeType)
    {
        return null;
    }

    public function getMaxMTime($mimeType)
    {
        $cacheId = $this->getMaxMTimeCacheId($mimeType);
        if ($cacheId) {
            $ret = Kwf_Assets_BuildCache::getInstance()->load($cacheId);
            if ($ret !== false) return $ret;
        }

        if (!Kwf_Assets_BuildCache::getInstance()->building && !Kwf_Config::getValue('assets.lazyBuild')) {
            throw new Kwf_Exception("Building assets is disabled (assets.lazyBuild). Please upload build contents.");
        }

        $maxMTime = 0;
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            $mTime = $i->getMTime();
            if ($mTime) {
                $maxMTime = max($maxMTime, $mTime);
            }
        }

        return $maxMTime;
    }

    public function getFilteredUniqueDependencies($mimeType)
    {
        return $this->_getFilteredUniqueDependencies($mimeType);
    }

    protected function _getFilteredUniqueDependencies($mimeType)
    {
        if (!isset($this->_cacheFilteredUniqueDependencies[$mimeType])) {
            $this->_cacheFilteredUniqueDependencies[$mimeType] = $this->getDependency()->getFilteredUniqueDependencies($mimeType);
            $defaults = array();
            foreach ($this->_providerList->getDefaultDependencies() as $i) {
                foreach ($i->getFilteredUniqueDependencies($mimeType) as $dep) {
                    if (!in_array($dep, $this->_cacheFilteredUniqueDependencies[$mimeType], true) && !in_array($dep, $defaults, true)) {
                        $defaults[] = $dep;
                    }
                }
            }
            $this->_cacheFilteredUniqueDependencies[$mimeType] = array_merge($defaults, $this->_cacheFilteredUniqueDependencies[$mimeType]);
        }
        return $this->_cacheFilteredUniqueDependencies[$mimeType];
    }

    public function getPackageContentsSourceMap($mimeType, $language)
    {
        if (!Kwf_Assets_BuildCache::getInstance()->building && !Kwf_Config::getValue('assets.lazyBuild')) {
            throw new Kwf_Exception("Building assets is disabled (assets.lazyBuild). Please upload build contents.");
        }

        $packageMap = Kwf_SourceMaps_SourceMap::createEmptyMap('');

        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/javascript; defer') $ext = 'defer.js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
        $packageMap->setFile($this->getPackageUrl($ext, $language));


        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            if ($i->getIncludeInPackage()) {
                $c = $i->getContentsPackedSourceMap($language);
                if (!$c) {
                    $map = Kwf_SourceMaps_SourceMap::createEmptyMap($i->getContentsPacked($language));
                } else {
                    $c = json_decode($c);
                    foreach ($c->sources as &$s) {
                        $s = '/assets/'.$s;
                    }
                    $map = new Kwf_SourceMaps_SourceMap($c, $i->getContentsPacked($language));
                }
                $packageMap->concat($map);
            }
        }

        return $packageMap->getMapContents(false);
        //$ret = '{"version":3, "file": "'.$file.'", "sources": ['.$retSources.'], "names": ['.$retNames.'], "mappings": "'.$retMappings.'"}';
    }

    /**
     * Get built contents of a package, to be used by eg. mails
     */
    public function getBuildContents($mimeType, $language)
    {
        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/javascript; defer') $ext = 'defer.js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; media=print') $ext = 'printcss';

        $cacheId = Kwf_Assets_Dispatcher::getCacheIdByPackage($this, $ext, $language);
        $ret = Kwf_Assets_BuildCache::getInstance()->load($cacheId);
        if ($ret === false || $ret === 'outdated') {
            if ($ret === 'outdated' && Kwf_Config::getValue('assets.lazyBuild') == 'outdated') {
                Kwf_Assets_BuildCache::getInstance()->building = true;
            } else if (Kwf_Config::getValue('assets.lazyBuild') !== true) {
                if (Kwf_Exception_Abstract::isDebug()) {
                    //proper error message on development server
                    throw new Kwf_Exception("Building assets is disabled (assets.lazyBuild). Please include package in build.");
                } else {
                    throw new Kwf_Exception_NotFound();
                }
            }
            $ret = $this->getPackageContents($mimeType, $language);
            Kwf_Assets_BuildCache::getInstance()->building = false;
        } else {
            $ret = $ret['contents'];
        }
        return $ret;
    }

    public function getPackageContents($mimeType, $language, $includeSourceMapComment = true)
    {
        if (!Kwf_Assets_BuildCache::getInstance()->building && !Kwf_Config::getValue('assets.lazyBuild')) {
            if (Kwf_Exception_Abstract::isDebug()) {
                //proper error message on development server
                throw new Kwf_Exception("Building assets is disabled (assets.lazyBuild). Please upload build contents.");
            } else {
                throw new Kwf_Exception_NotFound();
            }
        }

        $maxMTime = 0;
        $ret = '';
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            if ($i->getIncludeInPackage()) {
                if ($c = $i->getContentsPacked($language)) {
                    // $ret .= "/* *** $i */\n"; // attention: commenting this in breaks source maps
                    if (strpos($c, "//@ sourceMappingURL=") !== false && strpos($c, "//# sourceMappingURL=") !== false) {
                        throw new Kwf_Exception("contents must not contain sourceMappingURL");
                    }
                    $ret .= $c;
                    if (strlen($c) > 0 && substr($c, -1) != "\n") {
                        $ret .= "\n";
                    }
                }
            }
            $mTime = $i->getMTime();
            if ($mTime) {
                $maxMTime = max($maxMTime, $mTime);
            }
        }

        if ($mimeType == 'text/javascript') {
            $ret = str_replace(
                '{$application.assetsVersion}',
                Kwf_Assets_Dispatcher::getAssetsVersion(),
                $ret);
        }

        if ($includeSourceMapComment) {
            if ($mimeType == 'text/javascript') $ext = 'js';
            else if ($mimeType == 'text/javascript; defer') $ext = 'defer.js';
            else if ($mimeType == 'text/css') $ext = 'css';
            else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
            else throw new Kwf_Exception_NotYetImplemented();
            if ($ext == 'js' || $ext == 'defer.js') {
                $ret .= "\n//# sourceMappingURL=".$this->getPackageUrl($ext.'.map', $language)."\n";
            } else if ($ext == 'css' || $ext == 'printcss') {
                $ret .= "\n/*# sourceMappingURL=".$this->getPackageUrl($ext.'.map', $language)." */\n";
            }
        }

        return $ret;
    }

    public function toUrlParameter()
    {
        return get_class($this->_providerList).':'.$this->_dependencyName;
    }

    public static function fromUrlParameter($class, $parameter)
    {
        $param = explode(':', $parameter);
        $providerList = $param[0];
        return new $class(new $providerList, $param[1]);
    }

    public function getPackageUrl($ext, $language)
    {
        return Kwf_Setup::getBaseUrl().'/assets/dependencies/'.get_class($this).'/'.$this->toUrlParameter()
            .'/'.$language.'/'.$ext.'?v='.Kwf_Assets_Dispatcher::getAssetsVersion();
    }

    public function getPackageUrlsCacheId($mimeType, $language)
    {
        $ret = $this->_getCacheId($mimeType);
        if (!$ret) return $ret;
        return 'depPckUrls_'.$ret.'_'.$language;
    }

    public function getPackageUrls($mimeType, $language)
    {
        $cacheId = $this->getPackageUrlsCacheId($mimeType, $language);
        $ret = Kwf_Assets_BuildCache::getInstance()->load($cacheId);
        if ($ret !== false) {
            if (Kwf_Setup::getBaseUrl()) {
                foreach ($ret as $k=>$i) {
                    $ret[$k] = Kwf_Setup::getBaseUrl().$i;
                }
            }
            return $ret;
        }

        if (!Kwf_Assets_BuildCache::getInstance()->building && !Kwf_Config::getValue('assets.lazyBuild')) {
            throw new Kwf_Exception("Building assets is disabled (assets.lazyBuild). Please upload build contents.");
        }

        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/javascript; defer') $ext = 'defer.js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
        else throw new Kwf_Exception_NotYetImplemented();

        $ret = array();
        $hasContents = false;
        $includesDependencies = array();
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            if (!$i->getIncludeInPackage()) {
                if (in_array($i, $includesDependencies, true)) {
                    //include dependency only once
                    continue;
                }
                $includesDependencies[] = $i;
                if ($i instanceof Kwf_Assets_Dependency_HttpUrl) {
                    $ret[] = $i->getUrl();
                } else {
                    throw new Kwf_Exception('Invalid dependency that should not be included in package');
                }
            } else {
                $hasContents = true;
            }
        }

        if ($hasContents) {
            array_unshift($ret, $this->getPackageUrl($ext, $language));
        }

        return $ret;
    }

    public function serialize()
    {
        throw new Kwf_Exception("you should not serialize/cache Package, it does everything lazily");
    }

    public function unserialize($serialized)
    {
    }
}
