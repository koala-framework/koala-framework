<?php
class Vpc_Directories_Top_Model extends Vps_Model_FnF
{
    public function __construct($config = array())
    {
        $config = array(
            'columns' => array('id', 'name'),
            'primaryKey' => 'id',
            'data'=> array(
                array('id'=>1, 'name'=>'Foo'),
                array('id'=>2, 'name'=>'Bar'),
                array('id'=>3, 'name'=>'Baz'),
                array('id'=>4, 'name'=>'Foo1'),
                array('id'=>5, 'name'=>'Foo2'),
                array('id'=>6, 'name'=>'Foo3'),
                array('id'=>7, 'name'=>'Foo4'),
                array('id'=>8, 'name'=>'Foo5'),
            )
        );
        parent::__construct($config);
    }
}
