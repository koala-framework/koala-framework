<?php
class Vps_Component_Generator_DbId_DbIdTest extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_DbId_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
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
}
