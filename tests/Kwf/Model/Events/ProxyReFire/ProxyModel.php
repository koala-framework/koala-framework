<?php
class Kwf_Model_Events_ProxyReFire_ProxyModel extends Kwf_Model_Proxy
{
    public function __construct()
    {
        $config = array(
            'proxyModel' => 'Kwf_Model_Events_ProxyReFire_SourceModel'
        );
        parent::__construct($config);
    }
}
