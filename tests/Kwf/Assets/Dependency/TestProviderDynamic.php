<?php
class Kwf_Assets_Dependency_TestProviderDynamic extends Kwf_Assets_Provider_Abstract
{
    private $_testDynamic;
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'TestDynamic') {
            if (!isset($this->_testDynamic)) {
                $this->_testDynamic = new Kwf_Assets_Dependency_TestDependencyDynamic();
            }
            return $this->_testDynamic;
        }
        return null;
    }
}
