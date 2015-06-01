<?php
class Kwf_Assets_ModuleDeps_TestProviderList extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        $providers = array(
            new Kwf_Assets_ModuleDeps_TestJsClassProvider(KWF_PATH.'/tests', 'kwf/tests'),
        );
        $providers = array_merge($providers, self::getVendorProviders());
        $providers[] = new Kwf_Assets_CommonJs_Provider();
        parent::__construct($providers);
    }
}
