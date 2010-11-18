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
        $domain = Vps_Registry::get('config')->server->domain;

        $c = $this->_root->getPageByUrl('http://'.$domain.'/de/test', 'en');
        $this->assertEquals($c->componentId, 'root-master_test');
        $this->assertContains('test root-master_test', $c->render());
        $this->assertContains('/de/test/test2', $c->render());
        $this->assertContains('Vpc_Trl_Simple_Test_Component', $c->render());

        $c = $this->_root->getPageByUrl('http://'.$domain.'/en/test', 'en');
        $this->assertEquals($c->componentId, 'root-en_test');
        $this->assertContains('test root-en_test', $c->render());
        $this->assertContains('/en/test/test2', $c->render());
        $this->assertContains('Vpc_Trl_Simple_Test_Trl_Component', $c->render());

        $c = $this->_root->getPageByUrl('http://'.$domain.'/de/test/test2', 'en');
        $this->assertEquals($c->componentId, 'root-master_test_test2');
        $this->assertContains('test2 root-master_test_test2', $c->render());
        $this->assertContains('Vpc_Trl_Simple_Test_Test2_Component', $c->render());

        $c = $this->_root->getPageByUrl('http://'.$domain.'/en/test/test2', 'en');
        $this->assertEquals($c->componentId, 'root-en_test_test2');
        $this->assertContains('test2 root-en_test_test2', $c->render());
        $this->assertContains('Vpc_Chained_Trl_Component', $c->render());
    }

    public function testComponentClassContraint()
    {
        $c = $this->_root->getChildComponent('-en');
        $this->assertEquals($c->getChildComponent(
                array('componentClass' => 'Vpc_Trl_Simple_Test_Trl_Component.Vpc_Trl_Simple_Test_Component')
            )->componentId, 'root-en_test');
    }
}
