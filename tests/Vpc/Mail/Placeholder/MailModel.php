<?php
class Vpc_Mail_Placeholder_MailModel extends Vpc_Mail_Model
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('component_id' => 'root', 'subject' => 'Sehr geehrte%r:% %gender% %title% %lastname%')
            ),
            'primaryKey' => 'component_id',
            'columns' => array('component_id', 'subject')
        ));
        parent::__construct($config);
    }
}
