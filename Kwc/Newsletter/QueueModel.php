<?php
class Vpc_Newsletter_QueueModel extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_newsletter_queue';
    protected $_rowClass = 'Vpc_Newsletter_QueueRow';
    protected $_referenceMap = array(
        'Newsletter' => array(
            'column' => 'newsletter_id',
            'refModelClass' => 'Vpc_Newsletter_Model'
        )
    );
}
