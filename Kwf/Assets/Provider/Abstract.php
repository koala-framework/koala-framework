<?php
abstract class Kwf_Assets_Provider_Abstract
{
    /**
     * @var Kwf_Assets_ProviderList_Abstract
     */
    protected $_providerList;

    abstract public function getDependency($dependencyName);

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        return array();
    }

    public function getDefaultDependencies()
    {
        return array();
    }

    public function getDependencyNameByAlias($aliasDependencyName)
    {
        return null;
    }

    public function setProviderList(Kwf_Assets_ProviderList_Abstract $providerList)
    {
        $this->_providerList = $providerList;
    }
}
