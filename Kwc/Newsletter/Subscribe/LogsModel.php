<?php
class Kwc_Newsletter_Subscribe_LogsModel extends Kwf_Model_Db
{
    protected $_table = 'kwc_newsletter_subscriber_logs';

    protected function _init()
    {
        $this->_referenceMap['Subscriber'] = array(
            'column' => 'subscriber_id',
            'refModelClass' => 'Kwc_Newsletter_Subscribe_Model'
        );
        parent::_init();
    }
}

