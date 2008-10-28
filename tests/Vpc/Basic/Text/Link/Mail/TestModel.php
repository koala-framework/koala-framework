<?php
class Vpc_Basic_Text_Link_Mail_TestModel extends Vpc_Basic_LinkTag_Mail_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
            )
        ));
        parent::__construct($config);
    }
}
