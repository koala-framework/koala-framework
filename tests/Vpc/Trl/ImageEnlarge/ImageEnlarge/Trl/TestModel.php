<?php
class Vpc_Trl_ImageEnlarge_ImageEnlarge_Trl_TestModel extends Vps_Model_Proxy
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnFFile(array(
            'primaryKey' => 'component_id',
            'uniqueIdentifier' => get_class($this).'-Proxy'
        ));
        parent::__construct($config);
    }
}
