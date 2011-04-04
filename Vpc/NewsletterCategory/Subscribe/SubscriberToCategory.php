<?php
class Vpc_NewsletterCategory_Subscribe_SubscriberToCategory extends Vps_Model_Db
{
    protected $_table = 'vpc_newsletter_subscribers_to_category';
    protected $_referenceMap = array(
        'Category' => array(
            'column' => 'category_id',
            'refModelClass' => 'Vpc_NewsletterCategory_CategoriesModel'
        ),
        'Subscriber' => array(
            'column' => 'subscriber_id',
            'refModelClass' => 'Vpc_NewsletterCategory_Subscribe_Model'
        )
    );
}
