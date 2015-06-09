<?php
class Kwf_Assets_ProviderList_Default extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        $providers = array();
        if (Kwf_Component_Data_Root::getComponentClass()) {
            $providers[] = new Kwf_Assets_Provider_Components(Kwf_Component_Data_Root::getComponentClass());
        } else {
            $providers[] = new Kwf_Assets_Provider_NoComponents();
        }
        if (file_exists('dependencies.ini')) {
            $providers[] = new Kwf_Assets_Provider_Ini('dependencies.ini');
        }
        $providers = array_merge($providers, self::getVendorProviders());
        $providers[] = new Kwf_Assets_Provider_IniNoFiles();
        $providers[] = new Kwf_Assets_Provider_Dynamic();
        $providers[] = new Kwf_Assets_TinyMce_Provider();
        $providers[] = new Kwf_Assets_Provider_KwfUtils();
        $providers[] = new Kwf_Assets_Provider_JsClassKwf();
        $providers[] = new Kwf_Assets_Provider_JsClass('./ext', 'web/ext', 'App');
        $providers[] = new Kwf_Assets_Provider_CssByJs(array('web/ext'));
        $providers[] = new Kwf_Assets_Provider_ExtTrl();
        $providers[] = new Kwf_Assets_Provider_DefaultAssets();
        $providers[] = new Kwf_Assets_Provider_ErrorHandler();
        $providers[] = new Kwf_Assets_Provider_AtRequires();
        $providers[] = new Kwf_Assets_Provider_ViewsUser();
        $providers[] = new Kwf_Assets_Modernizr_Provider();
        parent::__construct($providers);
    }
}
