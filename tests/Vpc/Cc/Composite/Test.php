<?php
/**
 * @group Cc
 * @group Composite_Cc
 * http://vps.vps.niko.vivid/vps/vpctest/Vpc_Cc_Paragraphs_Root/master/paragraphs
 * http://vps.vps.niko.vivid/vps/vpctest/Vpc_Cc_Paragraphs_Root/slave/paragraphs
 */
class Vpc_Cc_Composite_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Cc_Composite_Root');
    }

    public function testContents()
    {
        $domain = Zend_Registry::get('config')->server->domain;

        $c = $this->_root->getPageByUrl('http://'.$domain.'/master/composite', 'en');
        $this->assertEquals($c->componentId, 'root-master_composite');
        $html = $c->render();
        $this->assertTrue(substr_count($html, 'testx')==2);
        $this->assertContains('root-master_composite-test1', $html);
        $this->assertContains('root-master_composite-test2', $html);

        $c = $this->_root->getPageByUrl('http://'.$domain.'/slave/composite', 'en');
        $this->assertEquals($c->componentId, 'root-slave_composite');
        $html = $c->render();
        $this->assertTrue(substr_count($html, 'testx')==2);
        $this->assertContains('root-slave_composite-test1', $html);
        $this->assertContains('root-slave_composite-test2', $html);
    }
}
