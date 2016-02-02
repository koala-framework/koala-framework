<?php
class Kwf_Assets_CommonJs_JQueryPluginProvider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if (substr($dependencyName, 0, 18) == 'kwf-jquery-plugin/') {
            $dependencyName = substr($dependencyName, 18);
            $dep = $this->_providerList->findDependency($dependencyName);
            $dep = $this->_transformDep($dep);
            if ($dep instanceof Kwf_Assets_CommonJs_JQueryPluginDecoratorDependency) {
                $dep->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS, $this->_providerList->findDependency('jQuery'), 'jQuery');
            }
            return $dep;
        }
    }

    private function _transformDep($dep)
    {
        if ($dep instanceof Kwf_Assets_Dependency_Dependencies) {
            if ($dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES)) {
                throw new Kwf_Exception_NotYetImplemented();
            }
            $transformedDepsJs = array();
            $transformedDepsCss = array();
            foreach ($dep->getDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES) as $k=>$i) {
                if ($i->getMimeType() == 'text/javascript') {
                    $i = $this->_transformDep($i);
                    if ($i) $transformedDepsJs[$k] = $i;
                } else {
                    $transformedDepsCss[$k] = $i;
                }
            }
            if (count($transformedDepsJs) == 1) {
                //if a single js dependency transform into something that is compatible with commonjs
                $transformedDepsJs = array_values($transformedDepsJs);
                $ret = $transformedDepsJs[0];
                $ret->addDependency(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS, $this->_providerList->findDependency('jQuery'), 'jQuery');
                $ret->setDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $transformedDepsCss);
                return $ret;
            } else {
                $dep->setDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS, $transformedDepsJs);
                $dep->setDependencies(Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES, $transformedDepsCss);
                return $dep;
            }
        } else if ($dep instanceof Kwf_Assets_Dependency_File && $dep->getFileNameWithType() == 'jquery/dist/jquery.js') {
            return null;
        } else {
            if ($dep->getMimeType() == 'text/javascript') {
                return new Kwf_Assets_CommonJs_JQueryPluginDecoratorDependency($this->_providerList, $dep);
            } else {
                return $dep;
            }
        }
    }
}
