<?php
class Kwf_Model_FnF_ProxyFnFExtendedReference_ProxyModel extends Kwf_Model_Proxy
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = 'Kwf_Model_FnF_ProxyFnFExtendedReference_ChildModel';
        parent::__construct($config);
    }
}
