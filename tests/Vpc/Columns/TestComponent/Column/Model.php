<?php
class Vpc_Columns_TestComponent_Column_Model extends Vps_Model_FnF
{
    protected $_toStringField = 'foo';

    public function __construct()
    {
        $config = array(
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id' => '3000-1', 'foo'=>'foo'),
                array('component_id' => '3000-2', 'foo'=>'bar'),
                array('component_id' => '3000-3', 'foo'=>'baz'),
            )
        );
        parent::__construct($config);
    }
}
