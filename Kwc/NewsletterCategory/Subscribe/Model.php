<?php
class Kwc_NewsletterCategory_Subscribe_Model extends Kwc_Newsletter_Subscribe_Model
{
    protected $_rowClass = 'Kwc_NewsletterCategory_Subscribe_Row';

    protected $_dependentModels = array(
        'ToCategory' => 'Kwc_NewsletterCategory_Subscribe_SubscriberToCategory'
    );
}
