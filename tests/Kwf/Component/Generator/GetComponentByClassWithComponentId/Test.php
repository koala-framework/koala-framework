<?php
class Vps_Component_Generator_GetComponentByClassWithComponentId_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_GetComponentByClassWithComponentId_Root');
    }

    public function testByClass2()
    {
        $this->assertEquals(3, count($this->_root->getComponentsByClass('Vpc_Basic_Empty_Component')));
    }
}
