<?php
class Kwf_Assets_KwfUtils_TestProviderList extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        parent::__construct(array(
            new Kwf_Assets_Provider_KwfUtils(),
            new Kwf_Assets_Provider_JsClass(KWF_PATH.'/tests', 'kwf/tests'),
            new Kwf_Assets_Provider_Ini(KWF_PATH.'/dependencies.ini'),
            new Kwf_Assets_Provider_Ini(VENDOR_PATH.'/koala-framework/library-extjs2/dependencies.ini'),
            new Kwf_Assets_Provider_Ini(VENDOR_PATH.'/koala-framework/library-jquery/dependencies.ini'),
        ));
    }
}
