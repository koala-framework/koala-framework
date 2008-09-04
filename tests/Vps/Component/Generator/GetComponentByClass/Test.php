<?php
class Vps_Component_Generator_GetComponentByClass_Test extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_GetComponentByClass_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }
    public function testByClass()
    {
        $this->assertEquals(3, count($this->_root->getComponentsByClass('Vpc_Basic_Empty_Component')));
        $this->assertEquals(1, count($this->_root
                ->getComponentsByClass('Vpc_Basic_Empty_Component', array('id'=>'-1'))));
    }
}
