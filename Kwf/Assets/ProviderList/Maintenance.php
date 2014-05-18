<?php
class Kwf_Assets_ProviderList_Maintenance extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        $providers = array();
//         $providers[] = new Kwf_Assets_Provider_Ini(KWF_PATH.'/dependencies.ini');
        //TODO cache in Kwf_Cache_SimpleStatic?, share with Kwf_Assets_ProviderList_Default
        foreach (glob("vendor/*/*") as $i) {
            if (is_dir($i) && file_exists($i.'/dependencies.ini')) {
                $config = new Zend_Config_Ini($i.'/dependencies.ini', 'config');
                if ($config->provider == 'Kwf_Assets_Provider_Ini') {
                    $providers[] = new Kwf_Assets_Provider_Ini($i.'/dependencies.ini');
                } else if ($config->provider) {
                    throw new Kwf_Exception("Unknown dependencies provider: '{$config->provider}'");
                }
            }
        }
        $providers[] = new Kwf_Assets_Provider_Ini(KWF_PATH.'/Kwf/Controller/Action/Maintenance/dependencies.ini');
        $providers[] = new Kwf_Assets_Provider_IniNoFiles();
        $providers[] = new Kwf_Assets_Provider_ExtTrl();
        $providers[] = new Kwf_Assets_Provider_DefaultAssets();
        $providers[] = new Kwf_Assets_Provider_ErrorHandler();
        parent::__construct($providers);
    }
}
