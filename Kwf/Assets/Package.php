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
            $cacheId = 'depPckMaxMTime_'.str_replace(array('.'), '_', $this->_dependencyName).'_'.str_replace(array('/', ' ', ';', '='), '_', $mimeType);
            $ret = Kwf_Assets_Cache::getInstance()->load($cacheId);
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
            Kwf_Assets_Cache::getInstance()->save($maxMTime, $cacheId);

            //save generated caches for clear-cache-watcher
            if ($mimeType == 'text/javascript') $ext = 'js';
            else if ($mimeType == 'text/css') $ext = 'css';
            else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
            else throw new Kwf_Exception_NotYetImplemented();
            $fileName = 'cache/assets/package-max-mtime-'.$ext;
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
        }
        return $this->_cacheFilteredUniqueDependencies[$mimeType];
    }

    public function getPackageContents($mimeType, $language)
    {
        $maxMTime = 0;
        $ret = '';
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            if ($i->getIncludeInPackage()) {
                if ($c = $i->getContentsPacked($language)) {
                    //$ret .= "/* *** ".$i->getFileName()." *"."/\n";
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

    public function getPackageUrls($mimeType, $language)
    {
        $baseUrl = Kwf_Registry::get('config')->server->baseUrl;
        if (get_class($this->_providerList) == 'Kwf_Assets_ProviderList_Default') { //only cache for default providerList, so cacheId doesn't have to contain only dependencyName
            $cacheId = 'depPckUrls_'.$this->_dependencyName.'_'.str_replace(array('/', ' ', ';', '='), '_', $mimeType).'_'.$language;
            $ret = Kwf_Assets_Cache::getInstance()->load($cacheId);
            if ($ret !== false) return $ret;
        }

        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
        else throw new Kwf_Exception_NotYetImplemented();

        $ret = array();
        $ret[] = $baseUrl.'/assets/dependencies/'.get_class($this).'/'.$this->toUrlParameter()
            .'/'.$language.'/'.$ext.'?v='.Kwf_Assets_Dispatcher::getAssetsVersion();
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
                    $ret[] = $baseUrl.'/assets/dependencies/'.get_class($i).'/'.$i->toUrlParameter().'/'.$language.'/'.$ext.'?t='.$i->getMTime();
                }
            }
            $mTime = $i->getMTime();
            if ($mTime) {
                $maxMTime = max($maxMTime, $mTime);
            }
        }

        if (isset($cacheId)) {
            Kwf_Assets_Cache::getInstance()->save($ret, $cacheId);
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
