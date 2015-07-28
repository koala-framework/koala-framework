<?php
class Kwf_Assets_DependencyWithComponents_TestProviderList extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        parent::__construct(array(
            new Kwf_Assets_Provider_Ini(dirname(__FILE__).'/ProviderTestDependencies.ini'),
            new Kwf_Assets_Provider_IniNoFiles(),
            new Kwf_Assets_Components_Provider('Kwf_Assets_DependencyWithComponents_Root')
        ));
    }
}
