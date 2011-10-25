<?php
/**
 * @group Generator_GetComponentByClassSubPage
 */
class Kwf_Component_Generator_GetComponentByClassSubPage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_GetComponentByClassSubPage_Root');
    }

    public function testById()
    {
        $this->assertNotNull($this->_root->getComponentById(2));
        $this->assertEquals(2, count($this->_root->getComponentsByClass('Kwc_Basic_Empty_Component')));
    }
}
