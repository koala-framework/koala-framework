<?php
class Vpc_Mail_Image_Mail_Model extends Vpc_Mail_Model
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('component_id' => 'root', 'subject' => 'ImageTest')
            ),
            'primaryKey' => 'component_id',
            'columns' => array('component_id', 'subject')
        ));
        parent::__construct($config);
    }
}
