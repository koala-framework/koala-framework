<?php
class Kwf_Assets_Provider_DefaultAssets extends Kwf_Assets_Provider_Abstract
{
    public function getDependency($dependencyName)
    {
        return null;
    }

    public function getDefaultDependencies()
    {
        return array(
            $this->_providerList->findDependency('Kwf.AssetsVersion'),
            $this->_providerList->findDependency('Kwf.Trl')
        );
    }
}
