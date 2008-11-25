<?php
class Vpc_Box_Title_PagesModel extends Vps_Model_FnF
{
    protected $_data = array(
    array('id'=>1, 'pos'=>1, 'visible'=>true, 'name'=>'Foo1', 'filename' => 'home',
            'parent_id'=>null, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
    array('id'=>2, 'pos'=>2, 'visible'=>true, 'name'=>'Foo2', 'filename' => 'foo1',
            'parent_id'=>null, 'component'=>'table', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
    array('id'=>3, 'pos'=>1, 'visible'=>true, 'name'=>'Foo3', 'filename' => 'foo2',
            'parent_id'=>null, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
    array('id'=>4, 'pos'=>2, 'visible'=>true, 'name'=>'Foo4', 'filename' => 'foo3',
            'parent_id'=>3, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
    array('id'=>5, 'pos'=>1, 'visible'=>true, 'name'=>'Foo5', 'filename' => 'foo5',
            'parent_id'=>null, 'component'=>'table', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
    array('id'=>6, 'pos'=>2, 'visible'=>true, 'name'=>'Foo6', 'filename' => 'foo6',
            'parent_id'=>5, 'component'=>'empty', 'is_home'=>false, 'category' =>'main', 'hide'=>false)
    );
}
