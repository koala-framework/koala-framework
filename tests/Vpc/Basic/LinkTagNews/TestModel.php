<?php
class Vpc_Basic_LinkTagNews_TestModel extends Vpc_Basic_LinkTag_News_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('component_id' => 5100, 'news_id' => 501), // this will be deleted
                array('component_id' => 5200, 'news_id' => 503),
            ),
            'primaryKey' => 'component_id'
        ));
        parent::__construct($config);
    }
}
