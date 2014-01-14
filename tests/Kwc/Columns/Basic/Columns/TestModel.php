<?php
class Kwc_Columns_Basic_Columns_TestModel extends Kwf_Model_FnF
{
    public function __construct(array $config = array())
    {
        $config = array(
            'primaryKey' => 'component_id',
            'data' => array(
                array('component_id' => '3000-1', 'type'=>'2col-50_50'),
                array('component_id' => '3000-2', 'type'=>'2col-75_25'),
                array('component_id' => '3000-3', 'type'=>'3col-33_33_33'),
                array('component_id' => '3000-4', 'type'=>'3col-25_50_25')
            )
        );
        parent::__construct($config);
    }
}
