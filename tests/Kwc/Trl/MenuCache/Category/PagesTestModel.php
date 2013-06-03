<?php
class Kwc_Trl_MenuCache_Category_PagesTestModel extends Kwf_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home_de', 'custom_filename' => false,
                  'parent_id'=>'root-master-main', 'component'=>'test', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root-master'),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                  'parent_id'=>1, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-master'),
            array('id'=>3, 'pos'=>2, 'visible'=>false, 'name'=>'Test2', 'filename' => 'test2', 'custom_filename' => false,
                  'parent_id'=>1, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-master'),
            array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'Test4', 'filename' => 'test4', 'custom_filename' => false,
                  'parent_id'=>2, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-master'),

            array('id'=>5, 'pos'=>1, 'visible'=>true, 'name'=>'Test5', 'filename' => 'test5', 'custom_filename' => false,
                  'parent_id'=>'root-master-bottom', 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-master'),
            array('id'=>6, 'pos'=>1, 'visible'=>true, 'name'=>'Test6', 'filename' => 'test6', 'custom_filename' => false,
                  'parent_id'=>5, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-master'),

            array('id'=>7, 'pos'=>2, 'visible'=>false, 'name'=>'Test7', 'filename' => 'test7', 'custom_filename' => false,
                  'parent_id'=>'root-master-main', 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-master'),
    );
}
