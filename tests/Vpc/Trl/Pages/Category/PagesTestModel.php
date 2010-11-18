<?php
class Vpc_Trl_Pages_Category_PagesTestModel extends Vps_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home_de',
                  'parent_id'=>'root-master-main', 'component'=>'test', 'is_home'=>true, 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test',
                  'parent_id'=>1, 'component'=>'test', 'is_home'=>false, 'hide'=>false),
            array('id'=>3, 'pos'=>2, 'visible'=>false, 'name'=>'Test2', 'filename' => 'test2',
                  'parent_id'=>1, 'component'=>'test', 'is_home'=>false, 'hide'=>false),
    );
}
