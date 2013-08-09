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

    public function getPackageContents($mimeType)
    {
        $it = new Kwf_Assets_Dependency_RecursiveIterator($this);
        $it = new RecursiveIteratorIterator($it);
        $it = new Kwf_Assets_Dependency_MimeTypeFilterItrator($it, $mimeType);
        $ret = '';
        $includesDependencies = array();
        foreach ($it as $i) {
            if ($i->getIncludeInPackage()) {
                if (in_array($i, $includesDependencies, true)) {
                    //include dependency only once
                    continue;
                }
                if ($c = $i->getContents()) {
                    $includesDependencies[] = $i;
                    $ret .= $c."\n";
                }
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

    public function getPackageUrls($mimeType)
    {
        if ($mimeType == 'text/javascript; charset=utf-8') $ext = 'js';
        else if ($mimeType == 'text/css') $ext = 'css';
        else throw new Kwf_Exception_NotYetImplemented();

        $ret = array();
        $ret[] = '/assets/'.get_class($this).'/'.$this->toUrlParameter().'/'.$ext;
        $it = new Kwf_Assets_Dependency_RecursiveIterator($this);
        $it = new RecursiveIteratorIterator($it);
        $it = new Kwf_Assets_Dependency_MimeTypeFilterItrator($it, $mimeType);
        $includesDependencies = array();
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
                $ret[] = '/assets/'.get_class($i).'/'.$i->toUrlParameter().'/'.$ext;
            }
        }
        return $ret;
    }
}
