<?php
class Kwc_Newsletter_TestLogModel extends Kwc_Newsletter_LogModel
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'columns' => array('id', 'newsletter_id', 'start', 'stop', 'count', 'countErrors'),
            'data'=> array()
        ));
        parent::__construct($config);
    }
}
