<?php
class Kwf_Component_FindHome_Root_Domain_Category_Model extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home', 'custom_filename' => false,
              'parent_id'=>'root-at-main', 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root-at'),

        array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
              'parent_id'=>'root-at-main', 'component'=>'text', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-at'),

        array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test2', 'custom_filename' => false,
              'parent_id'=>'root-si-main', 'component'=>'text', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root-at'),

        array('id'=>4, 'pos'=>3, 'visible'=>true, 'name'=>'Special', 'filename' => 'special', 'custom_filename' => false,
              'parent_id'=>'root-si-main', 'component'=>'text', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-at'),

        array('id'=>5, 'pos'=>4, 'visible'=>true, 'name'=>'SpecialC', 'filename' => 'special_c', 'custom_filename' => false,
              'parent_id'=>'root-si-main', 'component'=>'text', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-at'),

        array('id'=>6, 'pos'=>5, 'visible'=>true, 'name'=>'test3', 'filename' => 'test3', 'custom_filename' => false,
              'parent_id'=>'root-at-main', 'component'=>'text', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-at'),
    );
}
