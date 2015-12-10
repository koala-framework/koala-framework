<?php
class Kwf_Assets_Package
    implements Kwf_Assets_Interface_UrlResolvable, Serializable
{
    protected $_providerList;
    protected $_dependencyName;
    protected $_dependency;
    protected $_enableLegacySupport = false;

    private $_cacheFilteredUniqueDependencies;
    private $_cssPackageContentsCache;
    private $_chunkedCssCache;

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

    //if enabled ie8+9 will be supported:
    //* multiple css files will be generated to avoid the <=ie9 4096 selectors limit
    //* text/css; ie8 file will be generated
    public function setEnableLegacySupport($v)
    {
        $this->_enableLegacySupport = $v;
        return $this;
    }

    /**
     * @return Kwf_Assets_Dependency_Abstract
     */
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

    /**
     * @return Kwf_Assets_ProviderList_Abstract
     */
    public function getProviderList()
    {
        return $this->_providerList;
    }

    //default impl doesn't cache, overriden in Package_Default
    protected function _getCacheId($mimeType)
    {
        return null;
    }

    public function getFilteredUniqueDependencies($mimeType)
    {
        return $this->_getFilteredUniqueDependencies($mimeType);
    }

    protected function _getFilteredUniqueDependencies($mimeType)
    {
        if ($mimeType == 'text/css; ie8') $mimeType = 'text/css';
        if (!isset($this->_cacheFilteredUniqueDependencies[$mimeType])) {
            $this->_cacheFilteredUniqueDependencies[$mimeType] = $this->getDependency()->getFilteredUniqueDependencies($mimeType);
        }
        return $this->_cacheFilteredUniqueDependencies[$mimeType];
    }

    /**
     * Get built contents of a package, to be used by eg. mails
     */
    public function getBuildContents($mimeType, $language)
    {
        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/javascript; defer') $ext = 'defer.js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; ie8') $ext = 'ie8.css';

        $cacheId = Kwf_Assets_Dispatcher::getInstance()->getCacheIdByPackage($this, $ext, $language);
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
            $ret = $this->getPackageContents($mimeType, $language)->getFileContents();
            Kwf_Assets_BuildCache::getInstance()->building = false;
        } else {
            $ret = $ret['contents'];
        }
        return $ret;
    }

    public function warmupDependencyCaches($dep, $language, $progress = null)
    {
        $cacheId = 'filtered-'.$dep->getIdentifier();
        if ($dep->usesLanguage()) {
            $cacheId .= '-'.$language;
        }

        $ret = Kwf_Assets_ContentsCache::getInstance()->load($cacheId);
        if ($ret === false) {

            $ret = $dep->getContentsPacked($language);
            if (!$ret) {
                throw new Kwf_Exception("Dependency '$dep' didn't return contents");
            }
            foreach ($this->getProviderList()->getFilters() as $filter) {
                if ($filter->getExecuteFor() == Kwf_Assets_Filter_Abstract::EXECUTE_FOR_DEPENDENCY
                    && $filter->getMimeType() == $dep->getMimeType()
                ) {
                    if ($progress) $progress->update(null, $dep->__toString().' '.str_replace('Kwf_Assets_Filter_', '', get_class($filter)));
                    $ret = $filter->filter($ret);
                }
            }
            Kwf_Assets_ContentsCache::getInstance()->save($ret, $cacheId);
        }

        return $ret;
    }

    private function _getFilterdDependencyContents($dep, $language)
    {
        return $this->warmupDependencyCaches($dep, $language);
    }

    private function _getCommonJsDeps($i, $language, &$data)
    {
        $ret = array();
        foreach ($i->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS) as $depName=>$dep) {
            $ret[$depName] = $dep->__toString();
            if (!isset($data[$dep->__toString()])) {
                $commonJsDeps = $this->_getCommonJsDeps($dep, $language, $data);
                $data[$dep->__toString()] = array(
                    'id' => $dep->__toString(),
                    'source' => $c = $this->_getFilterdDependencyContents($dep, $language)->getFileContentsInlineMap(false),
                    'sourceFile' => $dep->__toString(), //TODO
                    'deps' => $commonJsDeps,
                    'entry' => false
                );
            }
        }
        return $ret;
    }

    private function _buildPackageContents($mimeType, $language)
    {
        if (!Kwf_Assets_BuildCache::getInstance()->building && !Kwf_Config::getValue('assets.lazyBuild')) {
            if (Kwf_Exception_Abstract::isDebug()) {
                //proper error message on development server
                throw new Kwf_Exception("Building assets is disabled (assets.lazyBuild). Please upload build contents.");
            } else {
                throw new Kwf_Exception_NotFound();
            }
        }

        foreach ($this->_providerList->getProviders() as $provider) {
            $provider->initialize();
        }

        $packageMap = Kwf_SourceMaps_SourceMap::createEmptyMap('');
        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/javascript; defer') $ext = 'defer.js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; ie8') $ext = 'ie8.css';
        else throw new Kwf_Exception("Invalid mimeType: '$mimeType'");
        $packageMap->setFile($this->getPackageUrl($ext, $language));
        if ($mimeType == 'text/css' || $mimeType == 'text/css; ie8') {
            $packageMap->setMimeType('text/css');
        } else {
            $packageMap->setMimeType('text/javascript');
        }

        // ***** commonjs
        $commonJsData = array();
        $commonJsDeps = array();
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            if ($i->getIncludeInPackage()) {
                if (($mimeType == 'text/javascript' || $mimeType == 'text/javascript; defer') && $i->isCommonJsEntry()) {
                    $c = $this->_getFilterdDependencyContents($i, $language)->getFileContentsInlineMap(false);
                    $commonJsDeps = $this->_getCommonJsDeps($i, $language, $commonJsData);
                    $commonJsData[$i->__toString()] = array(
                        'id' => $i->__toString(),
                        'source' => $c,
                        'sourceFile' => $i->__toString(), //TODO
                        'deps' => $commonJsDeps,
                        'entry' => true
                    );
                }
            }
        }
        if ($commonJsData) {
            if ($mimeType == 'text/javascript; defer') {
                //in defer.js don't include deps that are already loaded in non-defer
                foreach ($this->_getFilteredUniqueDependencies('text/javascript') as $i) {
                    $data = array();
                    $commonJsDeps = $this->_getCommonJsDeps($i, $language, $data);
                    foreach (array_keys($data) as $key) {
                        if (isset($commonJsData[$key])) {
                            unset($commonJsData[$key]);
                        }
                    }
                }
            }
            $contents = 'window.require = '.Kwf_Assets_CommonJs_BrowserPack::pack(array_values($commonJsData)).";\n";
            $map = Kwf_SourceMaps_SourceMap::createFromInline($contents);
            $packageMap->concat($map);
        }

        // ***** non-commonjs, css
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            if ($i->getIncludeInPackage()) {
                if (!(($mimeType == 'text/javascript' || $mimeType == 'text/javascript; defer') && $i->isCommonJsEntry())) {
                    $map = $this->_getFilterdDependencyContents($i, $language);
                    if (strpos($map->getFileContents(), "//@ sourceMappingURL=") !== false && strpos($map->getFileContents(), "//# sourceMappingURL=") !== false) {
                        throw new Kwf_Exception("contents must not contain sourceMappingURL");
                    }
                    foreach ($map->getMapContentsData(false)->sources as &$s) {
                        $s = '/assets/'.$s;
                    }
                    // $ret .= "/* *** $i */\n"; // attention: commenting this in breaks source maps
                    $packageMap->concat($map);
                }
            }
        }

        if ($mimeType == 'text/javascript' || $mimeType == 'text/javascript; defer') {
            if ($uniquePrefix = Kwf_Config::getValue('application.uniquePrefix')) {
                $packageMap = Kwf_Assets_Package_Filter_UniquePrefix::filter($packageMap, $uniquePrefix);
            }
        }

        foreach ($this->getProviderList()->getFilters() as $filter) {
            if ($filter->getExecuteFor() == Kwf_Assets_Filter_Abstract::EXECUTE_FOR_PACKAGE
                && $filter->getMimeType() == $mimeType
            ) {
                $packageMap = $filter->filter($packageMap);
            }
        }

        return $packageMap;
    }

    public function getPackageContents($mimeType, $language, $includeSourceMapComment = true)
    {
        if ($mimeType == 'text/css' || $mimeType == 'text/css; ie8') {
            if (!isset($this->_cssPackageContentsCacheNoIe8Filter)) {
                $this->_cssPackageContentsCacheNoIe8Filter = $this->_buildPackageContents($mimeType, $language);
            }
            $packageMap = $this->_cssPackageContentsCacheNoIe8Filter;
            if ($mimeType == 'text/css') {
                //remove @ie8 {}
                $f = new Kwf_Assets_Filter_Css_Ie8Remove();
                $packageMap = $f->filter($packageMap);
            }
            if ($mimeType == 'text/css; ie8') {
                //remove all but @ie8 {}
                $f = new Kwf_Assets_Filter_Css_Ie8Only();
                $packageMap = $f->filter($packageMap);
            }
        } else {
            $packageMap = $this->_buildPackageContents($mimeType, $language);
        }

        if ($includeSourceMapComment) {
            $contents = $packageMap->getFileContents();
            if ($mimeType == 'text/javascript') $ext = 'js';
            else if ($mimeType == 'text/javascript; defer') $ext = 'defer.js';
            else if ($mimeType == 'text/css') $ext = 'css';
            else if ($mimeType == 'text/css; ie8') $ext = 'ie8.css';
            else throw new Kwf_Exception_NotYetImplemented();
            if ($ext == 'js' || $ext == 'defer.js') {
                $contents .= "\n//# sourceMappingURL=".$this->getPackageUrl($ext.'.map', $language)."\n";
            } else if ($ext == 'css') {
                $contents .= "\n/*# sourceMappingURL=".$this->getPackageUrl($ext.'.map', $language)." */\n";
            }
            $packageMap->setFileContents($contents);
        }

        return $packageMap;
    }

    private function _getChunkedContentsCount($mimeType, $language)
    {
        if (!$this->_enableLegacySupport || $mimeType != 'text/css') {
            return 1;
        } else {
            return count($this->_getChunkedContents($mimeType, $language));
        }
    }

    private function _getChunkedContents($mimeType, $language)
    {
        if (!$this->_enableLegacySupport || $mimeType != 'text/css') {
            throw new Kwf_Exception("no chunks enabled");
        } else {

            if (isset($this->_chunkedCssCache)) {
                return $this->_chunkedCssCache;
            }

            $contents = $this->getPackageContents($mimeType, $language);
            $filter = new Kwf_Assets_Filter_CssChunks();
            $this->_chunkedCssCache = $filter->filter($contents);
            return $this->_chunkedCssCache;
        }
    }

    public function toUrlParameter()
    {
        return get_class($this->_providerList).':'.$this->_dependencyName.($this->_enableLegacySupport ? ':l' : '');
    }

    public static function fromUrlParameter($class, $parameter)
    {
        $param = explode(':', $parameter);
        $providerList = $param[0];
        $ret = new $class(new $providerList, $param[1]);
        if (isset($param[2]) && $param[2] == 'l') {
            $ret->setEnableLegacySupport(true);
        }
        return $ret;
    }

    public function getPackageUrl($ext, $language)
    {
        return Kwf_Setup::getBaseUrl().'/assets/dependencies/'.get_class($this).'/'.$this->toUrlParameter()
            .'/'.$language.'/'.$ext.'?v='.Kwf_Assets_Dispatcher::getInstance()->getAssetsVersion();
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

        if ($mimeType == 'text/css; ie8' && !$this->_enableLegacySupport) {
            return array();
        }

        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/javascript; defer') $ext = 'defer.js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; ie8') $ext = 'ie8.css';
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
            $chunks = $this->_getChunkedContentsCount($mimeType, $language);
            if ($chunks > 1) {
                $urls = array();
                for ($i=0; $i<$chunks; $i++) {
                    $urls[] = $this->getPackageUrl($i.'.'.$ext, $language);
                }
            } else {
                $urls = array($this->getPackageUrl($ext, $language));
            }
            $ret = array_merge($urls, $ret);
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

    public function getUrlContents($extension, $language)
    {
        $sourceMap = false;
        if (substr($extension, -4) == '.map') {
            $extension = substr($extension, 0, -4);
            $sourceMap = true;
        }
        if ($extension == 'js') $mimeType = 'text/javascript';
        else if ($extension == 'ie8.css') $mimeType = 'text/css; ie8';
        else if ($extension == 'defer.js') $mimeType = 'text/javascript; defer';
        else if (substr($extension, -3) == 'css') $mimeType = 'text/css';
        else throw new Kwf_Exception_NotFound();

        if ($mimeType == 'text/css' && $extension != 'css') {
            $chunkNum = substr($extension, 0, -4);
            $chunks = $this->_getChunkedContents($mimeType, $language);
            $map = $chunks[$chunkNum];
        } else {
            $map = $this->getPackageContents($mimeType, $language);
        }
        if (!$sourceMap) {
            $contents = $map->getFileContents();
            if ($extension == 'js' || $extension == 'defer.js') $mimeType = 'text/javascript; charset=utf-8';
            else if (substr($extension, -3) == 'css') $mimeType = 'text/css; charset=utf-8';
        } else {
            $contents = $map->getMapContents(false);
            $mimeType = 'application/json';
        }
        $ret = array(
            'contents' => $contents,
            'mimeType' => $mimeType,
            'mtime' => time()
        );
        return $ret;
    }
}
