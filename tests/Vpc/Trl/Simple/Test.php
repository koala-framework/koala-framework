<?php
/**
 * @group Vpc_Trl
 *
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_Simple_Root/de/test
http://doleschal.vps.niko.vivid/vps/vpctest/Vpc_Trl_Simple_Root/en/test
 */
class Vpc_Trl_Simple_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_Simple_Root');
    }
    public function testIt()
    {
        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/de/test', 'en');
        $this->assertEquals($c->componentId, 'root-de_test');
        $this->assertContains('test root-de_test', $c->render());
        $this->assertContains('/de/test/test2', $c->render());
        $this->assertContains('Vpc_Trl_Simple_Test_Component', $c->render());

        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/en/test', 'en');
        $this->assertEquals($c->componentId, 'root-en_test');
        $this->assertContains('test root-de_test', $c->render());
        $this->assertContains('/en/test/test2', $c->render());
        $this->assertContains('Vpc_Trl_Simple_Test_Trl_Component', $c->render());

        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/de/test/test2', 'en');
        $this->assertEquals($c->componentId, 'root-de_test_test2');
        $this->assertContains('test2 root-de_test_test2', $c->render());
        $this->assertContains('Vpc_Trl_Simple_Test_Test2_Component', $c->render());

        $c = $this->_root->getPageByUrl('http://'.Vps_Registry::get('testDomain').'/en/test/test2', 'en');
        $this->assertEquals($c->componentId, 'root-en_test_test2');
        $this->assertContains('test2 root-de_test_test2', $c->render());
        $this->assertContains('Vpc_Chained_Trl_Component', $c->render());
    }
}
