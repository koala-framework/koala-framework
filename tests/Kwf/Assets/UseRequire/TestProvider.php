<?php
class Kwf_Assets_UseRequire_TestProvider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'A') {
            return new Kwf_Assets_UseRequire_TestDependency("A");
        } else if ($dependencyName == 'B') {
            return new Kwf_Assets_UseRequire_TestDependency("B");
        } else if ($dependencyName == 'C') {
            return new Kwf_Assets_UseRequire_TestDependency("C");
        } else if ($dependencyName == 'D') {
            return new Kwf_Assets_UseRequire_TestDependency("D");
        }
    }
    /*
    Build the following dependency tree:
                  A
                  ^
     -------------|-----------
     |                       |
  (uses)                 (requires)
     |                       |
     B                       C
     |                       |
 (requires)              (requires)
     |                       |
     -------------|-----------
                  D

    Desired Order:
    ACBD
    */
    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if ($dependency->getContentsPacked('en')->getFileContents() == 'A') {
            return array();
        } else if ($dependency->getContentsPacked('en')->getFileContents() == 'B') {
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES => array(
                    $this->_providerList->findDependency('A')
                )
            );
        } else if ($dependency->getContentsPacked('en')->getFileContents() == 'C') {
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(
                    $this->_providerList->findDependency('A')
                )
            );
        } else if ($dependency->getContentsPacked('en')->getFileContents() == 'D') {
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(
                    $this->_providerList->findDependency('B'),
                    $this->_providerList->findDependency('C'),
                )
            );
        }
    }
}
