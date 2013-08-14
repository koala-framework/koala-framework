<?php
/**
 * Used by unittests that have own dependencies in dependencies.ini
 */
class Kwf_Assets_Dependency_TestPackage extends Kwf_Assets_Dependency_Package
{
    protected $_testDependenciesIni;

    public function __construct($testDependenciesIni, $dependencyName = 'TestFiles')
    {
        $this->_testDependenciesIni = $testDependenciesIni;

        $testDependenciesIni = KWF_PATH.'/tests/'.str_replace('_', '/', $testDependenciesIni).'/dependencies.ini';
        $providers = array();
        $providers[] = new Kwf_Assets_Provider_Ini(KWF_PATH.'/dependencies.ini');
        $providers[] = new Kwf_Assets_Provider_Ini($testDependenciesIni);
        $providerList = new Kwf_Assets_ProviderList_Abstract($providers);
        parent::__construct($providerList, $dependencyName);
    }

    public static function fromUrlParameter($class, $parameter)
    {
        $param = explode(':', $parameter);
        $testDependenciesIni = $param[0];
        return new $class($testDependenciesIni, $param[1]);
    }

    public function toUrlParameter()
    {
        return $this->_testDependenciesIni.':'.$this->_dependencyName;
    }
}
