<?php
class Kwc_Newsletter_QueueModel extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_newsletter_queue';
    protected $_rowClass = 'Kwc_Newsletter_QueueRow';
    protected $_referenceMap = array(
        'Newsletter' => array(
            'column' => 'newsletter_id',
            'refModelClass' => 'Kwc_Newsletter_Model'
        )
    );
}
