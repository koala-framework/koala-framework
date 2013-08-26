<?php
class Kwc_Mail_Redirect_Mail_Model extends Kwc_Mail_Model
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'data' => array(
                array('component_id' => 'root', 'subject' => '%salutation_polite%')
            ),
            'primaryKey' => 'component_id',
            'columns' => array('component_id', 'subject')
        ));
        parent::__construct($config);
    }
}
