<?php
class Kwc_Menu_IsVisibleDynamic_Root_Model extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config = array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'f1', 'filename' => 'f1',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null, 'parent_subroot_id' => 'root'),
            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'f2', 'filename' => 'f2',
                  'parent_id'=>'root', 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null, 'parent_subroot_id' => 'root'),
            array('id'=>3, 'pos'=>3, 'visible'=>true, 'name'=>'f3', 'filename' => 'f3',
                  'parent_id'=>'root', 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null, 'parent_subroot_id' => 'root'),
        ));
        $config = array('proxyModel' => new Kwf_Model_FnF($config));
        parent::__construct($config);
    }

    protected function _setupFilters()
    {
    }
}
