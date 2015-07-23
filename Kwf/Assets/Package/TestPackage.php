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

        $testDependenciesIni = str_replace('_', '/', $testDependenciesIni).'/dependencies.ini';
        if (file_exists('tests/'.$testDependenciesIni)) {
            $testDependenciesIni = 'tests/'.$testDependenciesIni;
        } else if (file_exists(KWF_PATH.'/tests/'.$testDependenciesIni)) {
            $testDependenciesIni = KWF_PATH.'/tests/'.$testDependenciesIni;
        }
        $providers = array();
        $providers[] = new Kwf_Assets_Provider_Ini(KWF_PATH.'/dependencies.ini');
        $providers[] = new Kwf_Assets_Provider_Ini($testDependenciesIni);
        $providers = array_merge($providers, Kwf_Assets_ProviderList_Abstract::getVendorProviders());
        if (file_exists('dependencies.ini')) {
            $providers[] = new Kwf_Assets_Provider_Ini('dependencies.ini');
        }
        $providers[] = new Kwf_Assets_Provider_IniNoFiles();
        $providers[] = new Kwf_Assets_Provider_Components($rootComponentClass);
        $providers[] = new Kwf_Assets_Provider_Dynamic();
        $providers[] = new Kwf_Assets_TinyMce_Provider();
        $providers[] = new Kwf_Assets_Provider_AtRequires();
        $providers[] = new Kwf_Assets_Provider_ViewsUser();
        $providers[] = new Kwf_Assets_Provider_ErrorHandler();
        $providers[] = new Kwf_Assets_Provider_JsClassKwf();
        $providers[] = new Kwf_Assets_Modernizr_Provider();
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
