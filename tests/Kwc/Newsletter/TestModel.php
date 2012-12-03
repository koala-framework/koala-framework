<?php
class Kwc_Newsletter_TestModel extends Kwc_Newsletter_Model
{
    protected $_dependentModels = array(
        'Queue' => 'Kwc_Newsletter_TestQueueModel',
        'QueueLog' => 'Kwf_Model_FnF',
        'Log' => 'Kwc_Newsletter_TestLogModel',
        'Mail' => 'Kwc_Mail_Model'
    );
    protected $_rowClass = 'Kwc_Newsletter_TestRow';

    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'columns' => array('id', 'component_id', 'create_date', 'status', 'count_sent', 'last_sent_date'),
            'primaryKey' => 'id',
            'data'=> array(
                array('id' => 1, 'component_id'=>1, 'create_date'=>null, 'status' => 'start', 0, null),
                array('id' => 2, 'component_id'=>1, 'create_date'=>null, 'status' => 'start', 0, null),
                array('id' => 3, 'component_id'=>1, 'create_date'=>null, 'status' => 'pause', 0, null)
            )
        ));
        parent::__construct($config);
    }
}