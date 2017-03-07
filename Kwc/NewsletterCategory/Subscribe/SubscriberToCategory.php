<?php
class Kwc_NewsletterCategory_Subscribe_SubscriberToCategory extends Kwf_Model_Db
{
    protected $_table = 'kwc_newsletter_subscribers_to_category';
    protected $_rowClass = 'Kwc_NewsletterCategory_Subscribe_SubscriberToCategoryRow';
    protected $_referenceMap = array(
        'Category' => array(
            'column' => 'category_id',
            'refModelClass' => 'Kwc_NewsletterCategory_CategoriesModel'
        ),
        'Subscriber' => array(
            'column' => 'subscriber_id',
            'refModelClass' => 'Kwc_NewsletterCategory_Subscribe_Model'
        )
    );
}
