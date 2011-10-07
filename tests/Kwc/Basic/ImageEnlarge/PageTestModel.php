<?php
class Vpc_Basic_ImageEnlarge_PageTestModel extends Vps_Model_FnF
{
    protected $_data = array(
            array('id'=>1800, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo1',
                  'parent_id'=>'root', 'component'=>'imageWithoutSmall', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1801, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo2',
                  'parent_id'=>'root', 'component'=>'image', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1802, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo3',
                  'parent_id'=>'root', 'component'=>'image', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
            array('id'=>1803, 'pos'=>1, 'visible'=>true, 'name'=>'Foo', 'filename' => 'foo4',
                  'parent_id'=>'root', 'component'=>'imageWithOriginal', 'is_home'=>false, 'category' =>'main', 'hide'=>false),
        );
}
