<?php
class Vps_Component_Generator_GetComponentByClassWithComponentId_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_GetComponentByClassWithComponentId_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }
    public function testByClass2()
    {
        $this->assertEquals(3, count($this->_root->getComponentsByClass('Vpc_Basic_Empty_Component')));
    }
}
