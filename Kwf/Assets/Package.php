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
        return 'mtime_'.get_class($this->_providerList).'_'.str_replace(array('.'), '_', $this->_dependencyName).'_'.str_replace(array('/', ' ', ';', '='), '_', $mimeType);
    }

    public function getMaxMTime($mimeType)
    {
        $cacheId = $this->getMaxMTimeCacheId($mimeType);
        $ret = Kwf_Assets_BuildCache::getInstance()->load($cacheId);
        if ($ret !== false) return $ret;

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

        $retSources = '';
        $retNames = '';
        $retMappings = '';
        $previousFileLast = false;
        $previousFileSourcesCount = 0;
        $previousFileNamesCount = 0;
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            if ($i->getIncludeInPackage()) {
                $c = $i->getContentsPackedSourceMap($language);
                if (!$c) {
                    $packageContents = $i->getContentsPacked($language);
                    $sources = array();
                    if ($i instanceof Kwf_Assets_Dependency_File) {
                        $sources[] = $i->getFileNameWithType();
                    } else {
                        $sources[] = 'dynamic/'.get_class($i).'-'.uniqid();
                    }
                    $data = array(
                        "version" => 3,
                        //"file" => ,
                        "sources"=> $sources,
                        "names"=> array(),
                        "mappings" => 'AAAAA'.str_repeat(';', substr_count($packageContents, "\n")),
                        '_x_org_koala-framework_last' => array(
                            'source' => 0,
                            'originalLine' => 0,
                            'originalColumn' => 0,
                            'name' => 0,
                        )
                    );
                } else {
                    $data = json_decode($c, true);
                }
                if (!isset($data['_x_org_koala-framework_last'])) {
                    throw new Kwf_Exception("source map doesn't contain _x_org_koala-framework_last extension");
                }

                foreach ($data['sources'] as &$s) {
                    $s = '/assets/'.$s;
                }
                if ($data['sources']) {
                    $retSources .= ($retSources ? ',' : '').substr(json_encode($data['sources']), 1, -1);
                }
                if ($data['names']) {
                    $retNames .= ($retNames ? ',' : '').substr(json_encode($data['names']), 1, -1);
                }
                if ($previousFileLast) {
                    // adjust first by previous
                    if (substr($data['mappings'], 0, 6) == 'AAAAA,') $data['mappings'] = substr($data['mappings'], 6);
                    $str  = Kwf_Assets_Util_Base64VLQ::encode(0);
                    $str .= Kwf_Assets_Util_Base64VLQ::encode(-$previousFileLast['source'] + $previousFileSourcesCount);
                    $str .= Kwf_Assets_Util_Base64VLQ::encode(-$previousFileLast['originalLine']);
                    $str .= Kwf_Assets_Util_Base64VLQ::encode(-$previousFileLast['originalColumn']);
                    $str .= Kwf_Assets_Util_Base64VLQ::encode(-$previousFileLast['name'] + $previousFileNamesCount);
                    $str .= ",";
                    $data['mappings'] = $str . $data['mappings'];
                }
                $previousFileLast = $data['_x_org_koala-framework_last'];
                $previousFileSourcesCount = count($data['sources']);
                $previousFileNamesCount = count($data['names']);

                if ($retMappings) $retMappings .= ';';
                $retMappings .= $data['mappings'];
            }
        }

        //manually build json, names array can be relatively large and merging all entries would be slow
        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
        $file = $this->getPackageUrl($ext, $language);
        $ret = '{"version":3, "file": "'.$file.'", "sources": ['.$retSources.'], "names": ['.$retNames.'], "mappings": "'.$retMappings.'"}';
        return $ret;
    }

    public function getPackageContents($mimeType, $language)
    {
        if (!Kwf_Assets_BuildCache::getInstance()->building && !Kwf_Config::getValue('assets.lazyBuild')) {
            throw new Kwf_Exception("Building assets is disabled (assets.lazyBuild). Please upload build contents.");
        }

        $maxMTime = 0;
        $ret = '';
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            if ($i->getIncludeInPackage()) {
                if ($c = $i->getContentsPacked($language)) {
                    //$ret .= "/* *** $i */\n";
                    if (strpos($c, "//@ sourceMappingURL=") !== false && strpos($c, "//# sourceMappingURL=") !== false) {
                        throw new Kwf_Exception("contents must not contain sourceMappingURL");
                    }
                    $ret .= $c."\n";
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

        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
        else throw new Kwf_Exception_NotYetImplemented();

        if ($ext == 'js') {
            $ret .= "\n//# sourceMappingURL=".$this->getPackageUrl($ext.'.map', $language)."\n";
        } else if ($ext == 'css' || $ext == 'printcss') {
            $ret .= "\n/*# sourceMappingURL=".$this->getPackageUrl($ext.'.map', $language)." */\n";
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
        return 'depPckUrls_'.get_class($this->_providerList).'_'.$this->_dependencyName.'_'.str_replace(array('/', ' ', ';', '='), '_', $mimeType).'_'.$language;
    }

    public function getPackageUrls($mimeType, $language)
    {
        $cacheId = $this->getPackageUrlsCacheId($mimeType, $language);
        $ret = Kwf_Assets_BuildCache::getInstance()->load($cacheId);
        if ($ret !== false) return $ret;

        if (!Kwf_Assets_BuildCache::getInstance()->building && !Kwf_Config::getValue('assets.lazyBuild')) {
            throw new Kwf_Exception("Building assets is disabled (assets.lazyBuild). Please upload build contents.");
        }

        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
        else throw new Kwf_Exception_NotYetImplemented();

        $ret = array(
            $this->getPackageUrl($ext, $language)
        );
        $includesDependencies = array();
        $maxMTime = 0;
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
                    if (!$i instanceof Kwf_Assets_Interface_UrlResolvable) {
                        throw new Kwf_Exception("dependency that should not be in package must implement UrlResolvableInterface");
                    }
                    $ret[] = Kwf_Setup::getBaseUrl().'/assets/dependencies/'.get_class($i).'/'.$i->toUrlParameter().'/'.$language.'/'.$ext.'?t='.$i->getMTime();
                }
            }
            $mTime = $i->getMTime();
            if ($mTime) {
                $maxMTime = max($maxMTime, $mTime);
            }
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
