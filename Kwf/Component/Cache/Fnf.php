<?php
class Vps_Component_Cache_Fnf extends Vps_Component_Cache_Mysql
{
    public function __construct()
    {
        $this->_models = array (
            'cache' => 'Vps_Component_Cache_Fnf_Model',
            'url' => 'Vps_Component_Cache_Fnf_UrlModel',
            'urlParents' => 'Vps_Component_Cache_Fnf_UrlParentsModel',
        );
    }
}
