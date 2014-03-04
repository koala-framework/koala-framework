<?php
class Kwc_Basic_Link_Link_Model extends Kwc_Basic_Link_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'columns' => array(),
            'data'=> array(
                array('component_id'=>'1', 'text'=>'Testlink'),
            )
        ));
        parent::__construct($config);
    }
}
