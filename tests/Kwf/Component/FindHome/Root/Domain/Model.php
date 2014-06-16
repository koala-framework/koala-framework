<?php
class Kwf_Component_FindHome_Root_Domain_Model extends Kwc_Root_CategoryModel
{
    public function __construct($config = array())
    {
        $config['pageCategories'] = array(
            'main' => 'Main'
        );
        parent::__construct($config);
    }
}
