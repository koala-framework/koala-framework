<?php
class Vpc_Newsletter_TestQueueModel extends Vpc_Newsletter_QueueModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'columns' => array('id', 'newsletter_id', 'recipient_model', 'recipient_id', 'status', 'sent_date'),
            'primaryKey' => 'id',
            'data'=> array(
                array('id' => 1, 'newsletter_id'=>2, 'recipient_model'=>'Vpc_Newsletter_TestUserModel', 'recipient_id' => 1, 'status' => 'queued', 'sent_date' => null),
                array('id' => 2, 'newsletter_id'=>1, 'recipient_model'=>'Vpc_Newsletter_TestUserModel', 'recipient_id' => 1, 'status' => 'queued', 'sent_date' => null),
                array('id' => 3, 'newsletter_id'=>3, 'recipient_model'=>'Vpc_Newsletter_TestUserModel', 'recipient_id' => 1, 'status' => 'queued', 'sent_date' => null),
                array('id' => 4, 'newsletter_id'=>1, 'recipient_model'=>'Vpc_Newsletter_TestUserModel', 'recipient_id' => 1, 'status' => 'sent', 'sent_date' => null),
                array('id' => 5, 'newsletter_id'=>2, 'recipient_model'=>'Vpc_Newsletter_TestUserModel', 'recipient_id' => 1, 'status' => 'queued', 'sent_date' => null),
                array('id' => 6, 'newsletter_id'=>2, 'recipient_model'=>'Vpc_Newsletter_TestUserModel', 'recipient_id' => 2, 'status' => 'queued', 'sent_date' => null),
            )
        ));
        parent::__construct($config);
        $this->_referenceMap['Newsletter']['refModelClass'] = 'Vpc_Newsletter_TestModel';
    }
}
