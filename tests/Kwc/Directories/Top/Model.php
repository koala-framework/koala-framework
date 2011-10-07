<?php
class Vpc_Directories_Top_Model extends Vps_Model_FnF
{
    public function __construct($config = array())
    {
        $config = array(
            'columns' => array('id', 'name', 'component_id'),
            'primaryKey' => 'id',
            'data'=> array(
                array('id'=>1, 'name'=>'Foo', 'component_id'=>'root_directory'),
                array('id'=>2, 'name'=>'Bar', 'component_id'=>'root_directory'),
                array('id'=>3, 'name'=>'Baz', 'component_id'=>'root_directory'),
                array('id'=>4, 'name'=>'Foo1', 'component_id'=>'root_directory'),
                array('id'=>5, 'name'=>'Foo2', 'component_id'=>'root_directory'),
                array('id'=>6, 'name'=>'Foo3', 'component_id'=>'root_directory'),
                array('id'=>7, 'name'=>'Foo4', 'component_id'=>'root_directory'),
                array('id'=>8, 'name'=>'Foo5', 'component_id'=>'root_directory'),
            )
        );
        parent::__construct($config);
    }
}
