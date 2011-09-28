<?php
/**
 * @group Vps_Acl
 */
class Vps_Acl_Vpc_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Acl_Vpc_Root');
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
