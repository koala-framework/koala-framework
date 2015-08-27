<?php
class Kwf_Assets_ResponsiveEl_TestProviderList extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        $providers = array(
//             new Kwf_Assets_Provider_JsClass(KWF_PATH.'/tests', 'kwf/tests'),
        );
        $providers = array_merge($providers, self::getVendorProviders());
        $providers[] = new Kwf_Assets_ResponsiveEl_Provider();
        $providers[] = new Kwf_Assets_Provider_Ini(dirname(__FILE__).'/TestDependencies.ini');
        $providers[] = new Kwf_Assets_Provider_KwfCommonJs();
        parent::__construct($providers);
    }
}
