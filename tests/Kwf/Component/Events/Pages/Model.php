<?php
class Kwf_Component_Events_Pages_Model extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config = array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>1, 'name'=>'F1', 'filename' => 'f1',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null, 'parent_subroot_id' => 'root'),
            array('id'=>2, 'pos'=>2, 'visible'=>1, 'name'=>'F2', 'filename' => 'f2',
                  'parent_id'=>'1', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null, 'parent_subroot_id' => 'root'),
            array('id'=>3, 'pos'=>1, 'visible'=>1, 'name'=>'F3', 'filename' => 'f3',
                  'parent_id'=>'2', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null, 'parent_subroot_id' => 'root'),
            array('id'=>4, 'pos'=>1, 'visible'=>0, 'name'=>'F4', 'filename' => 'f4',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null, 'parent_subroot_id' => 'root'),
        ));
        $config = array('proxyModel' => new Kwf_Model_FnF($config));
        parent::__construct($config);
    }
}
