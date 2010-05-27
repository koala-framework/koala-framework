<?php
class Vpc_Basic_LinkTag_TestModel extends Vpc_Basic_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'1100', 'component'=>'test'),
                array('component_id'=>'1101', 'component'=>'test2')
            )
        ));
        parent::__construct($config);
    }
}
