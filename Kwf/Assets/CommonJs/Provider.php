<?php
class Kwf_Assets_CommonJs_Provider extends Kwf_Assets_Provider_Abstract
{
    public function __construct()
    {
    }

    public function getDependency($dependencyName)
    {
        return null;
    }

    public function getDependenciesForDependency($dependency)
    {
        if ($dependency->getMimeType() != 'text/javascript' && $dependency->getMimeType() != 'text/javascript; defer') {
            return array();
        }
        if (!$dependency->isEntry()) {
            return array();
        }
        if ($dependency instanceof Kwf_Assets_Dependency_File) {
            $ret = array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS => $this->_parseDependencies($dependency)
            );
            return $ret;
        }
        return array();
    }

    private function _parseDependencies($dependency)
    {
        $ret = array();
        $deps = Kwf_Assets_CommonJs_Parser::parse($dependency->getAbsoluteFileName());
        foreach ($deps as $dep) {
            if (substr($dep, 0, 2) == './') {
                $fn = $dependency->getFileNameWithType();
                $dir = substr($fn, 0, strrpos($fn, '/')+1);
                $dep = $dir . substr($dep, 2);
            }
            $d = $this->_providerList->findDependency($dep);
            if (!$d) throw new Kwf_Exception("Can't resolve dependency: require '$dep' for $dependency");
            $ret[$dep] = $d;
            foreach ($this->_parseDependencies($d) as $i) {
                $d->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS, $i);
            }
        }
        return $ret;
    }
}
