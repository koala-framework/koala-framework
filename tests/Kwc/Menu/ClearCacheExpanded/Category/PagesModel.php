<?php
class Kwc_Menu_ClearCacheExpanded_Category_PagesModel extends Kwc_Root_Category_GeneratorModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'test1', 'filename' => 'test1', 'custom_filename' => false,
                    'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                    array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'test3', 'filename' => 'test3', 'custom_filename' => false,
                        'parent_id'=>'1', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                        array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'test4', 'filename' => 'test4', 'custom_filename' => false,
                            'parent_id'=>'3', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                            array('id'=>10, 'pos'=>1, 'visible'=>true, 'name'=>'test10', 'filename' => 'test10', 'custom_filename' => false,
                                'parent_id'=>'4', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                                array('id'=>11, 'pos'=>1, 'visible'=>true, 'name'=>'test11', 'filename' => 'test11', 'custom_filename' => false,
                                    'parent_id'=>'10', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                    array('id'=>8, 'pos'=>1, 'visible'=>true, 'name'=>'test8', 'filename' => 'test8', 'custom_filename' => false,
                        'parent_id'=>'1', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                        array('id'=>9, 'pos'=>1, 'visible'=>true, 'name'=>'test9', 'filename' => 'test9', 'custom_filename' => false,
                            'parent_id'=>'8', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                array('id'=>5, 'pos'=>2, 'visible'=>true, 'name'=>'test5', 'filename' => 'test5', 'custom_filename' => false,
                    'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                    array('id'=>6, 'pos'=>1, 'visible'=>true, 'name'=>'test6', 'filename' => 'test6', 'custom_filename' => false,
                        'parent_id'=>'5', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                        array('id'=>7, 'pos'=>1, 'visible'=>true, 'name'=>'test7', 'filename' => 'test7', 'custom_filename' => false,
                            'parent_id'=>'6', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
                array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'test2', 'filename' => 'test2', 'custom_filename' => false,
                    'parent_id'=>'root-bottom', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root'),
            )
        ));
        parent::__construct($config);
    }
}
