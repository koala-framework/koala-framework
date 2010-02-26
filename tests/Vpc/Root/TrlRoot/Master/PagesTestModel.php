<?php
class Vpc_Root_TrlRoot_Master_PagesTestModel extends Vps_Model_FnF
{
    protected $_data = array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Home de', 'filename' => 'home_de',
                  'parent_id'=>'root-master', 'component'=>'static', 'is_home'=>true, 'hide'=>false),
            array('id'=>2, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test',
                  'parent_id'=>'root-master', 'component'=>'static', 'is_home'=>false, 'hide'=>false),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Test', 'filename' => 'test2',
                  'parent_id'=>'root-master', 'component'=>'static', 'is_home'=>false, 'hide'=>false),
    );
}
