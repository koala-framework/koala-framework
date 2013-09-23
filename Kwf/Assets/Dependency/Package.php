<?php
class Kwf_Assets_Dependency_Package extends Kwf_Assets_Dependency_Abstract
    implements Kwf_Assets_Dependency_UrlResolvableInterface
{
    protected $_providerList;
    protected $_dependencyName;
    protected $_dependency;
    protected $_param;
    public function __construct(Kwf_Assets_ProviderList_Abstract $providerList, $dependencyName)
    {
        $this->_providerList = $providerList;
        $this->_dependencyName = $dependencyName;
        $dep = array();
        $d = $providerList->findDependency($dependencyName);
        if (!$d) {
            throw new Kwf_Exception("Could not resolve dependency '$dependencyName'");
        }
        $this->_dependency = $d;
    }

    public function getDependencies()
    {
        return array($this->_dependency);
    }

    public function getPackageContents($mimeType, $language)
    {
        $it = new Kwf_Assets_Dependency_RecursiveIterator($this);
        $it = new Kwf_Assets_Dependency_UniqueFilterIterator($it);
        $it = new RecursiveIteratorIterator($it);
        $it = new Kwf_Assets_Dependency_MimeTypeFilterItrator($it, $mimeType);
        $ret = '';
        $fileNames = array();
        $maxMTime = 0;
        foreach ($it as $i) {
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
        if ($mimeType == 'text/javascript') $ext = 'js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else if ($mimeType == 'text/css; media=print') $ext = 'printcss';
        else throw new Kwf_Exception_NotYetImplemented();

        $ret = array();
        $ret[] = '/assets/dependencies/'.get_class($this).'/'.$this->toUrlParameter().'/'.$language.'/'.$ext;
        $it = new Kwf_Assets_Dependency_RecursiveIterator($this);
        $it = new Kwf_Assets_Dependency_UniqueFilterIterator($it);
        $it = new RecursiveIteratorIterator($it);
        $it = new Kwf_Assets_Dependency_MimeTypeFilterItrator($it, $mimeType);
        $includesDependencies = array();
        $maxMTime = 0;
        foreach ($it as $i) {
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
        return $ret;
    }
}
