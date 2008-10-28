<?php
class Vpc_Basic_LinkTagIntern_TestModel extends Vpc_Basic_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'1300', 'target'=>'1310')
            )
        ));
        parent::__construct($config);
    }
}
