<?php
class Kwf_Component_Cache_Fnf extends Kwf_Component_Cache_Mysql
{
    public function __construct()
    {
        $this->_models = array (
            'cache' => 'Kwf_Component_Cache_Fnf_Model',
            'url' => 'Kwf_Component_Cache_Fnf_UrlModel',
            'includes' => 'Kwf_Component_Cache_Fnf_IncludesModel',
        );
    }
}
