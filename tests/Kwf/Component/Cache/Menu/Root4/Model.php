<?php
class Kwf_Component_Cache_Menu_Root4_Model extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config = array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'f1', 'filename' => 'f1', 'parent_subroot_id'=>'root',
              'parent_id'=>'root', 'component'=>'page', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null),
            array('id'=>1, 'pos'=>2, 'visible'=>true, 'name'=>'f2', 'filename' => 'f2', 'parent_subroot_id'=>'root',
              'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null),
        ));
        $config = array('proxyModel' => new Kwf_Model_FnF($config));
        parent::__construct($config);
    }

    protected function _setupFilters()
    {
    }
}
