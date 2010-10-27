<?php
class Vps_Component_Cache_Menu_Root_Model extends Vps_Model_FnF
{
    public function __construct()
    {
        $config = array('data'=>array(
            array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'f1', 'filename' => 'f1',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'f2', 'filename' => 'f2',
                  'parent_id'=>'1', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'f3', 'filename' => 'f3',
                  'parent_id'=>'2', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
            array('id'=>4, 'pos'=>1, 'visible'=>true, 'name'=>'f4', 'filename' => 'f3',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false),
        ));
        parent::__construct($config);
    }
}
