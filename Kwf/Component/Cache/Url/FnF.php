<?php
class Kwf_Component_Cache_Url_FnF extends Kwf_Component_Cache_Url_Mysql
{
    public function __construct()
    {
        $this->_models = array (
            'url' => 'Kwf_Component_Cache_Url_Fnf_Model',
        );
    }
}
