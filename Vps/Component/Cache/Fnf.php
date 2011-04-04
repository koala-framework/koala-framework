<?php
class Vps_Component_Cache_Fnf extends Vps_Component_Cache_Mysql
{
    public function __construct()
    {
        $this->_models = array (
            'cache' => 'Vps_Component_Cache_Fnf_Model',
            'metaModel' => 'Vps_Component_Cache_Fnf_MetaModelModel',
            'metaRow' => 'Vps_Component_Cache_Fnf_MetaRowModel',
            'metaComponent' => 'Vps_Component_Cache_Fnf_MetaComponentModel',
            'metaChained' => 'Vps_Component_Cache_Fnf_MetaChainedModel',
            'url' => 'Vps_Component_Cache_Fnf_UrlModel',
            'urlParents' => 'Vps_Component_Cache_Fnf_UrlParentsModel',
        );
    }
}
