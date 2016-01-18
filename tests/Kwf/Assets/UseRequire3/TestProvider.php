<?php
class Kwf_Assets_UseRequire3_TestProvider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'A') {
            return new Kwf_Assets_UseRequire3_TestDependency("A");
        } else if ($dependencyName == 'B') {
            return new Kwf_Assets_UseRequire3_TestDependency("B");
        } else if ($dependencyName == 'C') {
            return new Kwf_Assets_UseRequire3_TestDependency("C");
        }
    }
    /*
    Build the following dependency tree:

     A (recursion!)
     ^
     |
 (requires)
     |
     C
     ^
     |
  (uses)
     |
     B
     ^
     |
 (requires)
     |
     A (<<< start)

    Desired Order:
    BAC, not BCA
    */
    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        if ($dependency->getContentsPacked('en')->getFileContents() == 'A') {
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(
                    $this->_providerList->findDependency('B'),
                )
            );
        } else if ($dependency->getContentsPacked('en')->getFileContents() == 'B') {
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_USES => array(
                    $this->_providerList->findDependency('C')
                )
            );
        } else if ($dependency->getContentsPacked('en')->getFileContents() == 'C') {
            return array(
                Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(
                    $this->_providerList->findDependency('A'),
                )
            );
        }
    }
}
