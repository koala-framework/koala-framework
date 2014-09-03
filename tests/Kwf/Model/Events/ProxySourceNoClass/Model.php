<?php
class Kwf_Model_Events_ProxySourceNoClass_Model extends Kwf_Model_Proxy
{
    public function __construct()
    {
        parent::__construct(array(
            'proxyModel' => new Kwf_Model_FnF()
        ));
    }
}
