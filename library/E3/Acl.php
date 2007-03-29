<?php
class E3_Acl extends Zend_Acl
{
    public function __construct()
    {
        $this->addRole(new Zend_Acl_Role('admin'));
        $this->add(new Zend_Acl_Resource('admin'));
        $this->add(new Zend_Acl_Resource('fe'));
        $this->allow('admin', 'admin');
        $this->allow('admin', 'fe');
    }
}
?>
