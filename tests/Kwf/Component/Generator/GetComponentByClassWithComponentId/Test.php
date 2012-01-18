<?php
class Kwf_Component_Generator_GetComponentByClassWithComponentId_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_GetComponentByClassWithComponentId_Root');
    }

    public function testByClass2()
    {
        $this->assertEquals(3, count($this->_root->getComponentsByClass('Kwc_Basic_None_Component')));
    }
}
