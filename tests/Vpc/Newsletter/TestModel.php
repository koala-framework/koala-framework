<?php
class Vpc_Newsletter_TestModel extends Vpc_Newsletter_Model
{
    protected $_dependentModels = array(
        'Queue' => 'Vpc_Newsletter_TestQueueModel',
        'Log' => 'Vpc_Newsletter_TestLogModel',
        'Mail' => 'Vpc_Mail_Model'
    );
    protected $_rowClass = 'Vpc_Newsletter_TestRow';

    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'columns' => array('id', 'component_id', 'create_date', 'status'),
            'primaryKey' => 'id',
            'data'=> array(
                array('id' => 1, 'component_id'=>1, 'create_date'=>null, 'status' => 'start'),
                array('id' => 2, 'component_id'=>1, 'create_date'=>null, 'status' => 'start'),
                array('id' => 3, 'component_id'=>1, 'create_date'=>null, 'status' => 'pause')
            )
        ));
        parent::__construct($config);
    }
}