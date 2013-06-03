<?php
class Kwc_Root_LanguageRoot_Language_PagesTestModel extends Kwf_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home_de', 'custom_filename' => false,
                  'parent_id'=>'root-de', 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root-de'),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                  'parent_id'=>'root-de', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-de'),

            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Home en', 'filename' => 'home_en', 'custom_filename' => false,
                  'parent_id'=>'root-en', 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root-en'),

            array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test', 'custom_filename' => false,
                  'parent_id'=>'root-fr', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'parent_subroot_id' => 'root-fr'),
            array('id'=>5, 'pos'=>1, 'visible'=>true, 'name'=>'Home fr', 'filename' => 'home_fr', 'custom_filename' => false,
                  'parent_id'=>4, 'component'=>'empty', 'is_home'=>true, 'hide'=>false, 'parent_subroot_id' => 'root-fr'),
    );
}
