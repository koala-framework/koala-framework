<?php
/**
 * @group Kwf_Acl
 */
class Kwf_Acl_Kwc_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Acl_Kwc_Root');
    }

    public function testKwcAcl()
    {
        $acl = new Kwf_Acl();
        $acl->add(new Zend_Acl_Resource('misc'));

        $acl->allow(null, 'misc');
        $config = $acl->getMenuConfig(null);
        $this->assertEquals(1, count($config));
        $this->assertEquals('url', $config[0]['type']);
    }
}
