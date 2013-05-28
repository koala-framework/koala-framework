<?php
class Kwc_Trl_Pages_Category_PagesTestModel extends Kwf_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home_de', 'custom_filename' => false,
                  'parent_id'=>'root-master-main', 'component'=>'test', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id'=>'root-master'),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                  'parent_id'=>1, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root-master'),
            array('id'=>3, 'pos'=>2, 'visible'=>false, 'name'=>'Test2', 'filename' => 'test2', 'custom_filename' => false,
                  'parent_id'=>1, 'component'=>'test', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id'=>'root-master'),
    );
}
