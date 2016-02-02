<?php
class Kwf_Assets_CommonJs_TestProvider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'A') {
            $ret = new Kwf_Assets_CommonJs_Dependency($this->_providerList, "console.log(A)", 'text/javascript', false);
            $ret->setIsCommonJsEntry(true);
            return $ret;
        } else if ($dependencyName == 'B') {
            return new Kwf_Assets_CommonJs_Dependency($this->_providerList, "console.log(B)", 'text/javascript', false);
        } else if ($dependencyName == 'C') {
            return new Kwf_Assets_CommonJs_Dependency($this->_providerList, "C", 'text/css', false);
        } else if ($dependencyName == 'D') {
            return new Kwf_Assets_CommonJs_Dependency($this->_providerList, "console.log(D)", 'text/javascript', false);
        }
    }

    /*
    Build the following dependency tree:
                  A.js (commonjsentry)
                  ^
     -------------|-----------
                             |
                             B.js (commonjs)
                -------------|
                |            |
                D.js (req)   C.css (requires)
    */
    public function getDependenciesForDependency($dependency)
    {
        if ($dependency instanceof Kwf_Assets_CommonJs_Dependency) {
            if ($dependency->getContentsPacked()->getFileContents() == 'console.log(A)') {
                return array(
                    Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_COMMONJS => array(
                        $this->_providerList->findDependency('B'),
                    )
                );
            } else if ($dependency->getContentsPacked()->getFileContents() == 'console.log(B)') {
                return array(
                    Kwf_Assets_Dependency_Abstract::DEPENDENCY_TYPE_REQUIRES => array(
                        $this->_providerList->findDependency('C'),
                        $this->_providerList->findDependency('D'),
                    )
                );
            }
        }
        return array();
    }
}
