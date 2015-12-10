<?php
class Kwf_Assets_Package
    implements Kwf_Assets_Interface_UrlResolvable, Serializable
{
    protected $_providerList;
    protected $_dependencyName;
    protected $_dependency;
    protected $_cacheFilteredUniqueDependencies;
    const cssFileRuleLimit = 4000;     //for IE8-9 only 4000 rules per file are possible, see http://stackoverflow.com/a/9906889/781662

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

    protected function _getFilteredUniqueDependenciesPart($mimeType, $language, $partNumber)
    {
        $deps = $this->_getFilteredUniqueDependencies($mimeType);
        if ($mimeType != 'text/css') {
            if ($partNumber > 0) throw new Kwf_Exception_NotFound();
            return $deps;
        }
        $curPartNum = 0;
        $ret = array();
        $ruleCount = 0;
        $fileCount = 0;
        foreach ($deps as $i) {
            if ($i->getIncludeInPackage()) {
                $c = $i->getContentsPacked($language);
                $assetRuleCount = self::countCssRules($c);
                if ($fileCount > 0 && $ruleCount + $assetRuleCount > self::cssFileRuleLimit) {
                    //schen gruaß vom ie8
                    $curPartNum++;
                    $ruleCount = 0;
                    $fileCount = 0;
                }
                $ruleCount += $assetRuleCount;
                $fileCount++;
                if ($curPartNum == $partNumber) {
                    $ret[] = $i;
                }
            }
        }
        return $ret;
    }

    public function getPackageContentsSourceMap($mimeType, $language, $partNumber)
    {
        if (!Kwf_Assets_BuildCache::getInstance()->building && !Kwf_Config::getValue('assets.lazyBuild')) {
            throw new Kwf_Exception("Building assets is disabled (assets.lazyBuild). Please upload build contents.");
        }

        $packageMap = Kwf_SourceMaps_SourceMap::createEmptyMap('');

        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/javascript; defer') $ext = 'defer.js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
        $packageMap->setFile($this->getPackageUrl($ext, $language, $partNumber));


        foreach ($this->_getFilteredUniqueDependenciesPart($mimeType, $language, $partNumber) as $i) {
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

        //currently only a single part number is supported
        //TODO support larger css with multiple parts
        $partNumber = 0;

        $cacheId = Kwf_Assets_Dispatcher::getInstance()->getCacheIdByPackage($this, $ext, $language, $partNumber);
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
            $ret = $this->getPackageContents($mimeType, $language, $partNumber);
            Kwf_Assets_BuildCache::getInstance()->building = false;
        } else {
            $ret = $ret['contents'];
        }
        return $ret;
    }

    //try to count the number of css rules
    //this is not very acurate but "good enough"
    public static function countCssRules($c)
    {
        // remove comments
        $c = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*'.'/!', '', $c);

        //remove contents in () to remove all , (eg rgb(1,2,3))
        $c = preg_replace('#\([^\)\(]*\)#', '', $c);
        $c = preg_replace('#\([^\)\(]*\)#', '', $c); //twice to get linear-gradient(-45deg, rgba(255, 255, 255, 0.15)...)

        //remove content: "x-slicer ... as used by extjs
        $c = preg_replace('#"[^"]*\"#', '', $c);

        $c = preg_replace('#box\-shadow:[^;}]+#m', '', $c);

        return substr_count($c, '}')  //rule blocks
             + substr_count($c, ',')  //plus additional selectors for a block
             - substr_count($c, '@'); //minus the } used by @media, @font-face etc
    }

    public function getPackageContentsPartCount($mimeType, $language)
    {
        $ret = 1;
        if ($mimeType == 'text/css') {
            $ruleCount = 0;
            $fileCount = 0;
            foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
                if ($i->getIncludeInPackage()) {
                    $c = $i->getContentsPacked($language);
                    $assetRuleCount = self::countCssRules($c);
                    if ($fileCount > 0 && $ruleCount + $assetRuleCount > self::cssFileRuleLimit) {
                        //schen gruaß vom ie8
                        $ret++;
                        $ruleCount = 0;
                        $fileCount = 0;
                    }
                    $ruleCount += $assetRuleCount;
                    $fileCount++;
                }
            }
        }
        return $ret;
    }

    public function getPackageContents($mimeType, $language, $partNumber, $includeSourceMapComment = true)
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
        $ruleCount = 0;
        foreach ($this->_getFilteredUniqueDependenciesPart($mimeType, $language, $partNumber) as $i) {
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
                Kwf_Assets_Dispatcher::getInstance()->getAssetsVersion(),
                $ret);
        }

        if ($includeSourceMapComment) {
            if ($mimeType == 'text/javascript') $ext = 'js';
            else if ($mimeType == 'text/javascript; defer') $ext = 'defer.js';
            else if ($mimeType == 'text/css') $ext = 'css';
            else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
            else throw new Kwf_Exception_NotYetImplemented();
            if ($ext == 'js' || $ext == 'defer.js') {
                $ret .= "\n//# sourceMappingURL=".$this->getPackageUrl($ext.'.map', $language, $partNumber)."\n";
            } else if ($ext == 'css' || $ext == 'printcss') {
                $ret .= "\n/*# sourceMappingURL=".$this->getPackageUrl($ext.'.map', $language, $partNumber)." */\n";
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

    public function getPackageUrl($ext, $language, $partNumber)
    {
        return Kwf_Setup::getBaseUrl().'/assets/dependencies/'.get_class($this).'/'.$this->toUrlParameter()
            .'/'.$language.'/'.$partNumber.'/'.$ext.'?v='.Kwf_Assets_Dispatcher::getInstance()->getAssetsVersion();
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
            for ($i=$this->getPackageContentsPartCount($mimeType, $language); $i>0; $i--) {
                array_unshift($ret, $this->getPackageUrl($ext, $language, $i-1));
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
