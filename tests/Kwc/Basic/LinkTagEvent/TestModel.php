<?php
class Kwc_Basic_LinkTagEvent_TestModel extends Kwc_Basic_LinkTag_Event_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('component_id' => 6100, 'event_id' => 601), // this will be deleted
                array('component_id' => 6200, 'event_id' => 603),
            ),
            'primaryKey' => 'component_id'
        ));
        parent::__construct($config);
    }
}
