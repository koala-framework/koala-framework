<?php
class Vps_Component_Generator_Indirect_IndirectTest extends PHPUnit_Framework_TestCase
{
    private $_root;
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Generator_Indirect_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testIndirect()
    {
        $classes = Vpc_Abstract::getIndirectChildComponentClasses(
            'Vps_Component_Generator_Indirect_Root', array('flags' => array('foo' => true)));
        $this->assertEquals(1, count($classes));
        $this->assertEquals('Vps_Component_Generator_Indirect_Flag', current($classes));

        $classes = Vpc_Abstract::getIndirectChildComponentClasses(
            'Vps_Component_Generator_Indirect_Root', array('flags' => array('bar' => true)));
        $this->assertEquals(1, count($classes));
        $this->assertEquals('Vps_Component_Generator_Indirect_Flag', current($classes));

        $classes = Vpc_Abstract::getIndirectChildComponentClasses(
            'Vps_Component_Generator_Indirect_Root', array('flags' => array('foobar' => true)));
        $this->assertEquals(1, count($classes));
        $this->assertEquals('Vps_Component_Generator_Indirect_Flag', current($classes));
    }
}
