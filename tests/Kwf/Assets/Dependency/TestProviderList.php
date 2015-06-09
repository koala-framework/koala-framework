<?php
class Kwf_Assets_Dependency_TestProviderList extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        parent::__construct(array(
            new Kwf_Assets_Provider_Ini(dirname(__FILE__).'/ProviderTestDependencies.ini'),
            new Kwf_Assets_Provider_IniNoFiles()
        ));
    }
}
