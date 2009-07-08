<?php
class Vpc_Newsletter_TestQueueLogModel extends Vpc_Newsletter_QueueLogModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'columns' => array('id', 'newsletter_id', 'start', 'stop', 'count', 'countErrors'),
            'data'=> array()
        ));
        parent::__construct($config);
    }
    }
