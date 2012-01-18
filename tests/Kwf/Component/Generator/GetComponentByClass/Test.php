<?php
class Kwf_Component_Generator_GetComponentByClass_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_GetComponentByClass_Root');
    }

    public function testByClass()
    {
        $this->assertEquals(3, count($this->_root->getComponentsByClass('Kwc_Basic_None_Component')));
        $this->assertEquals(1, count($this->_root
                ->getComponentsByClass('Kwc_Basic_None_Component', array('id'=>'-1'))));
    }
}
