<?php
class Kwf_Component_PagesController_PagesGeneratorActions_PagesModel extends Kwf_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root'),

            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test2', 'custom_filename' => false,
                  'parent_id'=>'2', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

            array('id'=>4, 'pos'=>3, 'visible'=>true, 'name'=>'Special', 'filename' => 'special', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'special', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

            array('id'=>5, 'pos'=>4, 'visible'=>true, 'name'=>'SpecialC', 'filename' => 'special_c', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'specialContainer', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),

            array('id'=>6, 'pos'=>5, 'visible'=>true, 'name'=>'test3', 'filename' => 'test3', 'custom_filename' => false,
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>7, 'pos'=>1, 'visible'=>true, 'name'=>'specialWithoutEdit', 'filename' => 'special_woe', 'custom_filename' => false,
                  'parent_id'=>'6', 'component'=>'specialWithoutEdit', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
    );
}
