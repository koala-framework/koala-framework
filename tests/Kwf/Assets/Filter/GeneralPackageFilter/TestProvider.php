<?php
class Kwf_Assets_Filter_GeneralPackageFilter_TestProvider extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        if ($dependencyName == 'Foo') {
            return new Kwf_Assets_Filter_GeneralPackageFilter_Dependency($this->_providerList);
        }
    }
}
