<?php
class Kwc_Mail_Redirect_Mail_Redirect_Model extends Kwc_Mail_Redirect_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'columns' => array('id', 'value', 'type', 'title'),
            'primaryKey' => 'id'
        ));
        parent::__construct($config);
    }
}
