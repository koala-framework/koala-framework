<?php
/**
 * @group ComponentLinkModifiers
 */
class Vps_Component_ComponentLinkModifiers_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_ComponentLinkModifiers_Root');
    }

    public function testIt()
    {
        $c = $this->_root->getComponentById('root_test');
        $html = $c->render();
        $this->assertEquals($html, '<a href="/vps/vpctest/Vps_Component_ComponentLinkModifiers_Root/page1" rel="">page1</a>foobar');
    }
}
