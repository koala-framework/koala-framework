<?php
class Kwf_Assets_Defer_TestProvider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'A') {
            return new Kwf_Assets_Defer_JsDependency($this->_providerList, "A", false);
        } else if ($dependencyName == 'B') {
            return new Kwf_Assets_Defer_JsDependency($this->_providerList, "B", true);
        } else if ($dependencyName == 'C') {
            return new Kwf_Assets_Defer_JsDependency($this->_providerList, "C", false);
        } else if ($dependencyName == 'D') {
            return new Kwf_Assets_Defer_JsDependency($this->_providerList, "D", false);
        }
    }
    /*
    Build the following dependency tree:
                  A
                  ^
     -------------|-----------
     |                       |
     C                       B (defer)
                             |
                             |
                             D
                             |
                             |
                             C (recursion)

    Desired Contents:
    non-defer: CA
    defer:     DB
    */
    public function getDependenciesForDependency($dependency)
    {
        if ($dependency instanceof Kwf_Assets_Defer_JsDependency) {
            if ($dependency->getContentsPacked()->getFileContents() == 'A') {
                return array(
                    Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(
                        $this->_providerList->findDependency('B'),
                        $this->_providerList->findDependency('C'),
                    )
                );
            } else if ($dependency->getContentsPacked()->getFileContents() == 'B') {
                return array(
                    Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(
                        $this->_providerList->findDependency('D')
                    )
                );
            } else if ($dependency->getContentsPacked()->getFileContents() == 'D') {
                return array(
                    Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(
                        $this->_providerList->findDependency('C')
                    )
                );
            }
        }
        return array();
    }
}
