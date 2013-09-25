<?php
class Kwf_Assets_Dependency_Package
    implements Kwf_Assets_Dependency_UrlResolvableInterface, Serializable
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
        $maxMTime = 0;
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            $mTime = $i->getMTime();
            if ($mTime) {
                $maxMTime = max($maxMTime, $mTime);
            }
        }
        return $maxMTime;
    }

    private function _getFilteredUniqueDependencies($mimeType)
    {
        if (!isset($this->_cacheFilteredUniqueDependencies[$mimeType])) {
            $it = new Kwf_Assets_Dependency_RecursiveIterator($this->getDependency());
            $it = new Kwf_Assets_Dependency_UniqueFilterIterator($it);
            $it = new RecursiveIteratorIterator($it);
            $it = new Kwf_Assets_Dependency_MimeTypeFilterItrator($it, $mimeType);
            $this->_cacheFilteredUniqueDependencies[$mimeType] = iterator_to_array($it);
        }
        return $this->_cacheFilteredUniqueDependencies[$mimeType];
    }

    public function getPackageContents($mimeType, $language)
    {
        $fileNames = array();
        $maxMTime = 0;
        $ret = '';
        foreach ($this->_getFilteredUniqueDependencies($mimeType) as $i) {
            if ($i->getIncludeInPackage()) {
                if ($i instanceof Kwf_Assets_Dependency_File) {
                    if ($i->getFileName()) {
                        if (in_array($i->getFileName(), $fileNames)) {
                            throw new Kwf_Exception("Duplicate file: ".$i->getFileName());
                        }
                        $fileNames[] = $i->getFileName();
                    }
                }
                if ($c = $i->getContentsPacked($language)) {
                    //$ret .= "/* *** ".$i->getFileName()." */\n";
                    $ret .= $c."\n";
                }
            }
            $mTime = $i->getMTime();
            if ($mTime) {
                $maxMTime = max($maxMTime, $mTime);
            }
        }

        $ret = str_replace(
            '{$application.maxAssetsMTime}',
            $maxMTime,
            $ret);

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
        if (get_class($this->_providerList) == 'Kwf_Assets_ProviderList_Default') {
            $cacheId = 'depPckUrls-'.$this->_dependencyName.'-'.$mimeType.'-'.$language;
            $ret = Kwf_Cache_SimpleStatic::fetch($cacheId);
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
                if (!$i instanceof Kwf_Assets_Dependency_UrlResolvableInterface) {
                    throw new Kwf_Exception("dependency that should not be in package must implement UrlResolvableInterface");
                }
                $ret[] = '/assets/dependencies/'.get_class($i).'/'.$i->toUrlParameter().'/'.$language.'/'.$ext.'?t='.$i->getMTime();
            }
            $mTime = $i->getMTime();
            if ($mTime) {
                $maxMTime = max($maxMTime, $mTime);
            }
        }
        $ret[0] .= '?t='.$maxMTime;

        if (isset($cacheId)) {
            Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        }
        return $ret;
    }

    public function serialize()
    {
        $ret = array();
        foreach (get_object_vars($this) as $k=>$i) {
            if ($k == '_dependency') { //don't serialize _dependency, re-create lazily if required
                continue;
            }
            if ($k == '_cacheFilteredUniqueDependencies') { //don't serialize _cacheFilteredUniqueDependencies, re-create lazily if required
                continue;
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
