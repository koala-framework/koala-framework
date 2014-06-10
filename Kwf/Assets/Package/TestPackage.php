<?php
/**
 * Used by unittests that have own dependencies in dependencies.ini
 */
class Kwf_Assets_Package_TestPackage extends Kwf_Assets_Package
{
    protected $_testDependenciesIni;
    protected $_rootComponentClass;

    public function __construct($testDependenciesIni, $dependencyName = 'TestFiles', $rootComponentClass = null)
    {
        $this->_testDependenciesIni = $testDependenciesIni;
        if (is_null($rootComponentClass)) {
            $rootComponentClass = Kwf_Component_Data_Root::getComponentClass();
        }
        $this->_rootComponentClass = $rootComponentClass;

        $testDependenciesIni = KWF_PATH.'/tests/'.str_replace('_', '/', $testDependenciesIni).'/dependencies.ini';
        $providers = array();
        $providers[] = new Kwf_Assets_Provider_Ini(KWF_PATH.'/dependencies.ini');
        $providers[] = new Kwf_Assets_Provider_Ini($testDependenciesIni);
        $providers[] = new Kwf_Assets_Provider_IniNoFiles();
        $providers[] = new Kwf_Assets_Provider_Components($rootComponentClass);
        $providers[] = new Kwf_Assets_Provider_Dynamic();
        $providers[] = new Kwf_Assets_Provider_KwfUtils();
        $providers[] = new Kwf_Assets_Modernizr_Provider();
        $providers[] = new Kwf_Assets_Provider_DefaultAssets();
        $providers[] = new Kwf_Assets_Provider_ErrorHandler();
        $providerList = new Kwf_Assets_ProviderList_Abstract($providers);
        parent::__construct($providerList, $dependencyName);
    }

    public static function fromUrlParameter($class, $parameter)
    {
        $param = explode(':', $parameter);
        $testDependenciesIni = $param[0];
        $rootComponentClass = $param[1];
        if ($rootComponentClass) {
            Kwf_Component_Data_Root::setComponentClass($rootComponentClass);
        }
        return new $class($testDependenciesIni, $param[2]);
    }

    public function toUrlParameter()
    {
        return $this->_testDependenciesIni.':'.$this->_rootComponentClass.':'.$this->_dependencyName;
    }
}
