<?php
class Kwf_Assets_Filter_GeneralDepFilter_TestProvider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'Foo1') {
            return new Kwf_Assets_Filter_GeneralDepFilter_Dependency1($this->_providerList);
        } else if ($dependencyName == 'Foo2') {
            return new Kwf_Assets_Filter_GeneralDepFilter_Dependency2($this->_providerList);
        }
    }

    public function getDependenciesForDependency($dependency)
    {
        if ($dependency instanceof Kwf_Assets_Filter_GeneralDepFilter_Dependency1) {
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(
                    $this->_providerList->findDependency('Foo2')
                )
            );
        }
        return array();
    }
}
