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
            $cacheId = 'depPckMaxMTime_'.$this->_dependencyName.'_'.str_replace(array('/', ' ', ';', '='), '_', $mimeType);
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
        }
        return $maxMTime;
    }

    private function _getFilteredUniqueDependencies($mimeType)
    {
        if (!isset($this->_cacheFilteredUniqueDependencies[$mimeType])) {
            $it = new Kwf_Assets_Dependency_Iterator_Recursive($this->getDependency());
            $it = new Kwf_Assets_Dependency_Iterator_UniqueFilter($it);
            $it = new RecursiveIteratorIterator($it);
            $it = new Kwf_Assets_Dependency_Iterator_MimeTypeFilter($it, $mimeType);
            $this->_cacheFilteredUniqueDependencies[$mimeType] = array();
            foreach ($it as $i) {
                $this->_cacheFilteredUniqueDependencies[$mimeType][] = $i;
            }
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
/* TODO find a better solution for that, using Default Admin isn't compatible with tests as wrong Component dependencies are used
        if ($mimeType == 'text/javascript') {
            $ret = str_replace(
                '{$application.maxAssetsMTime}',
                Kwf_Assets_Package_Default::getInstance('Admin')->getMaxMTime('text/javascript'),
                $ret);
        }
*/
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
        $ret[] = '/assets/dependencies/'.get_class($this).'/'.$this->toUrlParameter().'/'.$language.'/'.$ext;
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
                    $ret[] = '/assets/dependencies/'.get_class($i).'/'.$i->toUrlParameter().'/'.$language.'/'.$ext.'?t='.$i->getMTime();
                }
            }
            $mTime = $i->getMTime();
            if ($mTime) {
                $maxMTime = max($maxMTime, $mTime);
            }
        }
        $ret[0] .= '?t='.$maxMTime;

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
