<?php
class E3_Acl extends Zend_Acl
{
    public function __construct()
    {
        $this->add(new Zend_Acl_Resource('fe'));
        $this->addRole(new Zend_Acl_Role('admin'));
        $this->allow('admin', 'fe');
    }
}
?>
