<?php
class Kwf_Assets_TinyMce_Provider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'KwfTinyMce') {
            return new Kwf_Assets_TinyMce_BuildDependency();
        }
    }

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        $deps = array();
        if ($dependency instanceof Kwf_Assets_TinyMce_BuildDependency) {
            $d = $this->_providerList->findDependency('jQuery');
            if (!$d) throw new Kwf_Exception("Didn't find dependency");
            $deps[Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES][] = $d;

        }
        return $deps;
    }
}
