<?php
class Kwf_Assets_ProviderList_Maintenance extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        $providers = array();
        $providers[] = new Kwf_Assets_Provider_Ini(VENDOR_PATH.'/koala-framework/extjs2/dependencies.ini');
        $providers[] = new Kwf_Assets_Provider_Ini(KWF_PATH.'/dependencies.ini');
        $providers[] = new Kwf_Assets_Provider_Ini(KWF_PATH.'/Kwf/Controller/Action/Maintenance/dependencies.ini');
        $providers[] = new Kwf_Assets_Provider_JsClassKwf();
        $providers[] = new Kwf_Assets_Provider_IniNoFiles();
        $providers[] = new Kwf_Assets_Provider_ExtTrl();
        $providers[] = new Kwf_Assets_Provider_DefaultAssets();
        $providers[] = new Kwf_Assets_Provider_ErrorHandler();
        $providers[] = new Kwf_Assets_TinyMce_Provider();
        $providers[] = new Kwf_Assets_Provider_BowerBuiltFile(VENDOR_PATH.'/bower_components/jquery');
        parent::__construct($providers);
    }
}
