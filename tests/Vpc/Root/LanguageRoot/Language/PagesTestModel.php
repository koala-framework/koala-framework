<?php
class Vpc_Root_LanguageRoot_Language_PagesTestModel extends Vps_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home_de',
                  'parent_id'=>'root-de', 'component'=>'empty', 'is_home'=>true, 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test',
                  'parent_id'=>'root-de', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),

            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Home en', 'filename' => 'home_en',
                  'parent_id'=>'root-en', 'component'=>'empty', 'is_home'=>true, 'hide'=>false),

            array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test',
                  'parent_id'=>'root-fr', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>5, 'pos'=>1, 'visible'=>true, 'name'=>'Home fr', 'filename' => 'home_fr',
                  'parent_id'=>4, 'component'=>'empty', 'is_home'=>true, 'hide'=>false),
    );
}
