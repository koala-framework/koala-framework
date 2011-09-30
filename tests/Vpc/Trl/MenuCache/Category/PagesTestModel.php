<?php
class Vpc_Trl_MenuCache_Category_PagesTestModel extends Vps_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home_de',
                  'parent_id'=>'root-master-main', 'component'=>'test', 'is_home'=>true, 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test',
                  'parent_id'=>1, 'component'=>'test', 'is_home'=>false, 'hide'=>false),
            array('id'=>3, 'pos'=>2, 'visible'=>false, 'name'=>'Test2', 'filename' => 'test2',
                  'parent_id'=>1, 'component'=>'test', 'is_home'=>false, 'hide'=>false),
            array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'Test4', 'filename' => 'test4',
                  'parent_id'=>2, 'component'=>'test', 'is_home'=>false, 'hide'=>false),

            array('id'=>5, 'pos'=>1, 'visible'=>true, 'name'=>'Test5', 'filename' => 'test5',
                  'parent_id'=>'root-master-bottom', 'component'=>'test', 'is_home'=>false, 'hide'=>false),
            array('id'=>6, 'pos'=>1, 'visible'=>true, 'name'=>'Test6', 'filename' => 'test6',
                  'parent_id'=>5, 'component'=>'test', 'is_home'=>false, 'hide'=>false),
    );
}
