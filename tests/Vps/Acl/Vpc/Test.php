<?php
/**
 * @group Vps_Acl
 */
class Vps_Acl_Vpc_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Acl_Vpc_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testVpcAcl()
    {
        $acl = new Vps_Acl();
        $acl->add(new Zend_Acl_Resource('misc'));

        $acl->allow(null, 'misc');
        $config = $acl->getMenuConfig(null);
        $this->assertEquals(1, count($config));
        $this->assertEquals('url', $config[0]['type']);
    }
}
