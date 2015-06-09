<?php
class Kwf_Component_Plugin_Inherit_Root_PagesModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Xx1', 'filename' => 'xx1', 'custom_filename' => false,
              'parent_id'=>'root', 'component'=>'test1', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root'),
        array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Xx2', 'filename' => 'xx2', 'custom_filename' => false,
              'parent_id'=>'1', 'component'=>'test2', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
        array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Xx3', 'filename' => 'xx3', 'custom_filename' => false,
              'parent_id'=>'2', 'component'=>'test1', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
        array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'Xx4', 'filename' => 'xx4', 'custom_filename' => false,
              'parent_id'=>'3', 'component'=>'test1', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
        array('id'=>5, 'pos'=>2, 'visible'=>true, 'name'=>'Xx5', 'filename' => 'xx5', 'custom_filename' => false,
              'parent_id'=>'root', 'component'=>'test1', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
        array('id'=>6, 'pos'=>2, 'visible'=>true, 'name'=>'Xx6', 'filename' => 'xx6', 'custom_filename' => false,
              'parent_id'=>'5', 'component'=>'test1', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
    );
}
