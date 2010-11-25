<?php
class Vps_Component_Generator_DbId_DbIdTest extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_DbId_Root');
    }

    public function testDbId()
    {
        $page = $this->_root->getComponentById('root_static_1');
        $this->assertNotNull($page);
        $this->assertEquals($page->componentId, 'root_static_1');
        $this->assertEquals($page->dbId, 'test_1');

        $page = $this->_root->getComponentByDbId('test_1');
        $this->assertNotNull($page);
        $this->assertEquals($page->componentId, 'root_static_1');
        $this->assertEquals($page->dbId, 'test_1');
    }

    public function testRootByDbId()
    {
        $page = $this->_root->getComponentByDbId('root_static');
        $this->assertNotNull($page);
        $this->assertEquals($page->componentId, 'root_static');
        $this->assertEquals($page->dbId, 'root_static');
    }
}
