<?php
class Vpc_NewsletterCategory_Subscribe_Model extends Vpc_Newsletter_Subscribe_Model
{
    protected $_dependentModels = array(
        'ToCategory' => 'Vpc_NewsletterCategory_Subscribe_SubscriberToCategory'
    );
}
