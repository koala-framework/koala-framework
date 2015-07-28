<?php
class Kwf_Assets_ProviderList_Test extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct($iniFile)
    {
        $providers = array();
        if (Kwf_Component_Data_Root::getComponentClass()) {
            $providers[] = new Kwf_Assets_Components_Provider(Kwf_Component_Data_Root::getComponentClass());
        }
        $providers[] = new Kwf_Assets_Provider_Ini(KWF_PATH.'/dependencies.ini');
        $providers[] = new Kwf_Assets_Provider_Ini($iniFile);
        $providers[] = new Kwf_Assets_Provider_Dynamic();
        parent::__construct($providers);
    }
}
