<?php
class Kwc_Newsletter_Subscribe_Model extends Kwf_Model_Db
{
    protected $_table = 'kwc_newsletter_subscribers';
    protected $_rowClass = 'Kwc_Newsletter_Subscribe_Row';

    protected $_columnMappings = array(
        'Kwc_Mail_Recipient_Mapping' => array(
            'firstname' => 'firstname',
            'lastname' => 'lastname',
            'email' => 'email',
            'format' => 'format',
        ),
        'Kwc_Mail_Recipient_GenderMapping' => array(
            'gender' => 'gender',
        ),
        'Kwc_Mail_Recipient_TitleMapping' => array(
            'title' => 'title',
        ),
        'Kwc_Mail_Recipient_UnsubscribableMapping' => array(
            'unsubscribed' => 'unsubscribed',
        ),
    );

}
