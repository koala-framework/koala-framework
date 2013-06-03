<?php
class Kwf_Component_DependingOnRows_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                //10 linkt zu 2
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home', 'filename' => 'home', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root'),
                array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo', 'custom_filename' => false,
                    'parent_id'=>1, 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
                array('id'=>10, 'pos'=>10, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

                //20 linkt zu 20
                array('id'=>20, 'pos'=>30, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

                //30 linkt zu 31
                array('id'=>30, 'pos'=>30, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                    'parent_id'=>'root', 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
                array('id'=>31, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                    'parent_id'=>30, 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            )
        ));
        parent::__construct($config);
    }
}
