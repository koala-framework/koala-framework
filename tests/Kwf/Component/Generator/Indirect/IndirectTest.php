<?php
class Kwf_Component_Generator_Indirect_IndirectTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_Indirect_Root');
    }

    public function testIndirect()
    {
        $classes = Kwc_Abstract::getIndirectChildComponentClasses(
            'Kwf_Component_Generator_Indirect_Root', array('flags' => array('foo' => true)));
        $this->assertEquals(1, count($classes));
        $this->assertEquals('Kwf_Component_Generator_Indirect_Flag', current($classes));

        $classes = Kwc_Abstract::getIndirectChildComponentClasses(
            'Kwf_Component_Generator_Indirect_Root', array('flags' => array('bar' => true)));
        $this->assertEquals(1, count($classes));
        $this->assertEquals('Kwf_Component_Generator_Indirect_Flag', current($classes));

        $classes = Kwc_Abstract::getIndirectChildComponentClasses(
            'Kwf_Component_Generator_Indirect_Root', array('flags' => array('foobar' => true)));
        $this->assertEquals(1, count($classes));
        $this->assertEquals('Kwf_Component_Generator_Indirect_Flag', current($classes));
    }
}
