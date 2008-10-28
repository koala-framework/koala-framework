<?php
class Vpc_Basic_Text_Link_TestModel extends Vpc_Basic_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'1007-l1', 'component'=>'extern')
            )
        ));
        parent::__construct($config);
    }
}
