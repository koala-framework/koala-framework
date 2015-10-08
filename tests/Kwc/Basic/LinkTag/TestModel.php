<?php
class Kwc_Basic_LinkTag_TestModel extends Kwc_Basic_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'1100', 'component'=>'test', 'data'=>null),
                array('component_id'=>'1101', 'component'=>'test2', 'data'=>null)
            )
        ));
        parent::__construct($config);
    }
}
