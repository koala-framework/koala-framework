<?php
class Kwf_Component_PagesController_WithCategories_Category_PagesModel extends Kwf_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home', 'custom_filename' => false,
                  'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                  'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test2', 'custom_filename' => false,
                  'parent_id'=>2, 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root'),
    );
}
