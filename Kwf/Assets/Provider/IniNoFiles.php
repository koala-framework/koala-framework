<?php
//this provider is neccessary for dependencies that have only .dep but no .files
class Kwf_Assets_Provider_IniNoFiles extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        foreach ($this->_providerList->getProviders() as $provider) {
            if ($provider instanceof Kwf_Assets_Provider_Ini) {
                if ($provider->hasDependencyInConfig($dependencyName)) {
                    return $this->_providerList->findDependency($dependencyName.'IniDep');
                }
            }
        }
        return null;
    }
}
