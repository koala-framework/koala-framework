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

    public function getMaxMTime($mimeType)
    {
        if (get_class($this->_providerList) == 'Kwf_Assets_ProviderList_Default') { //only cache for default providerList, so cacheId doesn't have to contain only dependencyName
            $cacheId = 'mtime_'.str_replace(array('.'), '_', $this->_dependencyName).'_'.str_replace(array('/', ' ', ';', '='), '_', $mimeType);
            $ret = Kwf_Assets_BuildCache::getInstance()->load($cacheId);
            if ($ret !== false) return $ret;
        }

        $maxMTime = 0;
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            $mTime = $i->getMTime();
            if ($mTime) {
                $maxMTime = max($maxMTime, $mTime);
            }
        }

        if (isset($cacheId)) {
            Kwf_Assets_BuildCache::getInstance()->save($maxMTime, $cacheId);

            //save generated caches for clear-cache-watcher
            if ($mimeType == 'text/javascript') $ext = 'js';
            else if ($mimeType == 'text/css') $ext = 'css';
            else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
            else throw new Kwf_Exception_NotYetImplemented();
            $fileName = 'build/assets/package-max-mtime-'.$ext;
            if (!file_exists($fileName) || strpos(file_get_contents($fileName), $cacheId."\n") === false) {
                file_put_contents($fileName, $cacheId."\n", FILE_APPEND);
            }
        }
        return $maxMTime;
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
        $ret = '';
        $maps = array();
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            if ($i->getIncludeInPackage()) {
                $c = $i->getContentsPackedSourceMap($language);
                if (!$c) {
                    $packageContents = $i->getContentsPacked($language);
                    $f = tempnam('temp', 'dynass'); //TODO delete temp file
                    file_put_contents($f, $packageContents);
                    $data = array(
                        "version" => 3,
                        "file" => $f,
                        "sources"=> array(),
                        "names"=> array(),
                        "mappings" => array()
                    );
                    $c = json_encode($data);
                }
                $f = tempnam('temp', 'map'); //TODO delte temp file
                file_put_contents($f, $c);
                $maps[] = $f;
            }
        }
        if ($maps) {
            $outFile = tempnam('temp', 'sourcemap');
            $cmd = "PATH=\$PATH:/var/www/node/bin  ./node_modules/.bin/mapcat ".implode(' ', $maps);
            $cmd .= " --mapout $outFile.map";
            $cmd .= " --jsout $outFile";
            $cmd .= " 2>&1 ";
            $out = array();
            exec($cmd, $out, $retVal);
            if ($retVal) {
                throw new Kwf_Exception('mapcat failed: '.implode("\n", $out));
            }
            $ret = file_get_contents("$outFile.map");
            unlink("$outFile.map");
            unlink("$outFile");
            $ret = json_decode($ret);
            foreach ($ret->sources as &$i) {
                $i = '/assets/'.$i;
            }
            $ret = json_encode($ret);
        }
        return $ret;
    }

    public function getPackageContents($mimeType, $language)
    {
        $maxMTime = 0;
        $ret = '';
        $maps = array();
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

        $ret .= "\n//@ sourceMappingURL=".$this->getPackageUrl($ext.'.map', $language);

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

    public function getPackageUrls($mimeType, $language)
    {
        if (get_class($this->_providerList) == 'Kwf_Assets_ProviderList_Default') { //only cache for default providerList, so cacheId doesn't have to contain only dependencyName
            $cacheId = 'depPckUrls_'.$this->_dependencyName.'_'.str_replace(array('/', ' ', ';', '='), '_', $mimeType).'_'.$language;
            $ret = Kwf_Assets_BuildCache::getInstance()->load($cacheId);
            if ($ret !== false) return $ret;
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

        if (isset($cacheId)) {
            Kwf_Assets_BuildCache::getInstance()->save($ret, $cacheId);
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
