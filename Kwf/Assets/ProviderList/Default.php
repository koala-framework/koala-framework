<?php
class Kwf_Assets_ProviderList_Default extends Kwf_Assets_ProviderList_Abstract
{
    public function __construct()
    {
        $providers = array();
        if (Kwf_Component_Data_Root::getComponentClass()) {
            $providers[] = new Kwf_Assets_Provider_Components(Kwf_Component_Data_Root::getComponentClass());
        }
        $providers[] = new Kwf_Assets_Provider_Ini('dependencies.ini');
        if (defined('VKWF_PATH')) $providers[] = new Kwf_Assets_Provider_Ini(VKWF_PATH.'/dependencies.ini');
        $providers[] = new Kwf_Assets_Provider_Ini(KWF_PATH.'/dependencies.ini');
        $providers[] = new Kwf_Assets_Provider_Dynamic();
        parent::__construct($providers);
    }
}
