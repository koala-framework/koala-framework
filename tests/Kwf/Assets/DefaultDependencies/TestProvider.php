<?php
class Kwf_Assets_DefaultDependencies_TestProvider extends Kwf_Assets_Provider_Abstract
{
    public function getDefaultDependencies()
    {
        return array($this->_providerList->findDependency('Bar'));
    }

    public function getDependency($dependencyName)
    {
        return null;
    }
}
