<?php
class Kwf_Assets_Modernizr_Provider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'Modernizr') {
            return new Kwf_Assets_Modernizr_Dependency();
        } else if (substr($dependencyName, 0, 9) == 'Modernizr') {
            $feature = substr($dependencyName, 9);
            $m = $this->_providerList->findDependency('Modernizr');
            $m->addFeature($feature);
            return $m;
        }
    }

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if ($dependency->getMimeType() == 'text/css') {
            $contents = $dependency->getContentsSourceString();
            if (strpos($contents, '@include modernizr') === false) {
                return array();
            }
            if (preg_match_all('#@include modernizr(-no)?\(([a-z0-9]+)\)#i', $contents, $m)) {
                $ret = array();
                foreach (array_keys($m[2]) as $k) {
                    $test = trim($m[2][$k]);
                    $ret[] = $this->_providerList->findDependency('Modernizr'.ucfirst($test));
                }
                return array(
                    Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => $ret
                );
            }
        }
        return array();
    }
}
