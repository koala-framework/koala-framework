<?php
/**
 * @group Generator_GetComponentByClassSubPage
 */
class Vps_Component_Generator_GetComponentByClassSubPage_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_GetComponentByClassSubPage_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }
    public function testById()
    {
        $this->assertNotNull($this->_root->getComponentById(2));
        $this->assertEquals(2, count($this->_root->getComponentsByClass('Vpc_Basic_Empty_Component')));
    }
}
