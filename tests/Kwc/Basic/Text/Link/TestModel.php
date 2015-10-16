<?php
class Kwc_Basic_Text_Link_TestModel extends Kwc_Basic_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'1007-l1', 'component'=>'extern', 'data' => null)
            )
        ));
        parent::__construct($config);
    }
}
