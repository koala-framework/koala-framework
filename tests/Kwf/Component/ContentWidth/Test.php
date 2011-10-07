<?php
/**
 * @group Component_ContentWidth
 */
class Vps_Component_ContentWidth_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_ContentWidth_Root_Component');
    }

    public function testFullWidthPage()
    {
        $c = $this->_root->getComponentById('root_page');
        $this->assertEquals(800, $c->getComponent()->getContentWidth());
    }

    public function testChildHasSubtractedWidth()
    {
        $c = $this->_root->getComponentById('root_page-child');
        $this->assertEquals(790, $c->getComponent()->getContentWidth());
    }
}
