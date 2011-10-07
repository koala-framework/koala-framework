<?php
/**
 * @group Generator_GetComponentByClassSubPage
 */
class Vps_Component_Generator_GetComponentByClassSubPage_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_GetComponentByClassSubPage_Root');
    }

    public function testById()
    {
        $this->assertNotNull($this->_root->getComponentById(2));
        $this->assertEquals(2, count($this->_root->getComponentsByClass('Vpc_Basic_Empty_Component')));
    }
}
