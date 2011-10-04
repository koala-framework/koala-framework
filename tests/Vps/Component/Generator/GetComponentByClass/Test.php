<?php
class Vps_Component_Generator_GetComponentByClass_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_GetComponentByClass_Root');
    }

    public function testByClass()
    {
        $this->assertEquals(3, count($this->_root->getComponentsByClass('Vpc_Basic_Empty_Component')));
        $this->assertEquals(1, count($this->_root
                ->getComponentsByClass('Vpc_Basic_Empty_Component', array('id'=>'-1'))));
    }
}
