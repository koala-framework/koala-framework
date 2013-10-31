<?php
class Kwf_Assets_Ext4_TestProviderList extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        parent::__construct(array(
            new Kwf_Assets_Ext4_Provider(),
            new Kwf_Assets_Provider_JsClass(KWF_PATH.'/tests'),
            new Kwf_Assets_Provider_Ini(dirname(__FILE__).'/ProviderTestDependencies.ini'),
        ));
    }
}
