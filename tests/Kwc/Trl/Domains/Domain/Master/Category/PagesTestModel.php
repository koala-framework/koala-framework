<?php
class Kwc_Trl_Domains_Domain_Master_Category_PagesTestModel extends Kwf_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home_de', 'custom_filename' => false,
                  'parent_id'=>'root-at-master-main', 'component'=>'test', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id'=>'root-at-master'),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                  'parent_id'=>1, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root-at-master'),
            array('id'=>3, 'pos'=>2, 'visible'=>true, 'name'=>'Test2', 'filename' => 'test2', 'custom_filename' => false,
                  'parent_id'=>1, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root-at-master'),

            array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home_de', 'custom_filename' => false,
                  'parent_id'=>'root-hu-master-main', 'component'=>'test', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id'=>'root-hu-master'),
            array('id'=>5, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                  'parent_id'=>4, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root-hu-master'),
            array('id'=>6, 'pos'=>2, 'visible'=>true, 'name'=>'Test2', 'filename' => 'test2', 'custom_filename' => false,
                  'parent_id'=>4, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root-hu-master'),

            array('id'=>7, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home_de', 'custom_filename' => false,
                  'parent_id'=>'root-ro-master-main', 'component'=>'test', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id'=>'root-ro-master'),
            array('id'=>8, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                  'parent_id'=>7, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root-ro-master'),
            array('id'=>9, 'pos'=>2, 'visible'=>true, 'name'=>'Test2', 'filename' => 'test2', 'custom_filename' => false,
                  'parent_id'=>7, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root-ro-master'),
    );
}
