<?php
class Kwf_Assets_DependencyIni_TestProviderList extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        parent::__construct(array(
            new Kwf_Assets_Provider_Ini(dirname(__FILE__).'/dependencies1.ini'),
            new Kwf_Assets_Provider_Ini(dirname(__FILE__).'/dependencies2.ini'),
            new Kwf_Assets_Provider_IniNoFiles()
        ));
    }
}
