<?php
class Vpc_NewsletterCategory_Subscribe_Model extends Vpc_Newsletter_Subscribe_Model
{
    protected $_dependentModels = array(
        'ToPool' => 'Vpc_NewsletterCategory_Subscribe_SubscriberToPool'
    );
}
