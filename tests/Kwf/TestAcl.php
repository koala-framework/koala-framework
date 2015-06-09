<?php
class Kwf_TestAcl extends Kwf_Acl
{
    public function __construct()
    {
        parent::__construct();
        $this->add(new Zend_Acl_Resource('kwf_test'));
        $this->allow(null, 'kwf_test');
    }
}
