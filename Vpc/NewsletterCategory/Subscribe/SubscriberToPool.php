<?php
class Vpc_NewsletterCategory_Subscribe_SubscriberToPool extends Vps_Model_Db
{
    protected $_table = 'vpc_newsletter_subscribers_to_pool';
    protected $_referenceMap = array(
        'Pool' => array(
            'column' => 'pool_id',
            'refModelClass' => 'Vps_Util_Model_Pool'
        ),
        'Subscriber' => array(
            'column' => 'subscriber_id',
            'refModelClass' => 'Vpc_NewsletterCategory_Subscribe_Model'
        )
    );
}
