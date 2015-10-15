<?php
abstract class Kwf_Assets_Provider_Abstract
{
    /**
     * @var Kwf_Assets_ProviderList_Abstract
     */
    protected $_providerList;

    protected $_initialized = false;

    abstract public function getDependency($dependencyName);

    public function getDependenciesForDependency(Kwf_Assets_Dependency_Abstract $dependency)
    {
        return array();
    }

    /**
     * @deprecated doesn't exist anymore
     */
    public final function getDefaultDependencies()
    {
    }

    public function getDependencyNameByAlias($aliasDependencyName)
    {
        return null;
    }

    public function setProviderList(Kwf_Assets_ProviderList_Abstract $providerList)
    {
        $this->_providerList = $providerList;
    }

    public final function initialize()
    {
        if (!$this->_initialized) {
            $this->_initialized = true;
            $this->_initialize();
        }
    }

    protected function _initialize()
    {
    }
}
