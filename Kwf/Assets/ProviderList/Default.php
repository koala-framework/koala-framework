<?php
class Kwf_Assets_ProviderList_Default extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        $providers = array();
        if (Kwf_Component_Data_Root::getComponentClass()) {
            $providers[] = new Kwf_Assets_Provider_Components(Kwf_Component_Data_Root::getComponentClass());
        }
        $providers[] = new Kwf_Assets_Provider_Ini('dependencies.ini');
//         if (defined('VKWF_PATH')) $providers[] = new Kwf_Assets_Provider_Ini(VKWF_PATH.'/dependencies.ini');
//         $providers[] = new Kwf_Assets_Provider_Ini(KWF_PATH.'/dependencies.ini');

        //TODO cache in Kwf_Cache_SimpleStatic?, share with Kwf_Assets_ProviderList_Maintenance
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

        $providers[] = new Kwf_Assets_Provider_IniNoFiles();
        $providers[] = new Kwf_Assets_Provider_Dynamic();
        $providers[] = new Kwf_Assets_Provider_KwfUtils();
        $providers[] = new Kwf_Assets_Modernizr_Provider();
//         $providers[] = new Kwf_Assets_Ext4_Provider();
//         $providers[] = new Kwf_Assets_Ext4_Extensible_Provider();
        $providers[] = new Kwf_Assets_Provider_JsClassKwf();
        $providers[] = new Kwf_Assets_Provider_JsClass('./ext', 'web/ext', 'App');
//         $providers[] = new Kwf_Assets_Ext4_AclControllerProvider();
        $providers[] = new Kwf_Assets_Provider_CssByJs(array('kwf/Kwf_js/Ext4', 'web/ext'));
        $providers[] = new Kwf_Assets_Provider_ExtTrl();
//         $providers[] = new Kwf_Assets_Ext4_TrlProvider();
        $providers[] = new Kwf_Assets_Provider_DefaultAssets();
        $providers[] = new Kwf_Assets_Provider_ErrorHandler();
        parent::__construct($providers);
    }
}
