<?php
/**
 * @group ComponentLinkModifiers
 */
class Kwf_Component_ComponentLinkModifiers_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_ComponentLinkModifiers_Root');
    }

    public function testIt()
    {
        $c = $this->_root->getComponentById('root_test');
        $html = $c->render();
        $this->assertEquals($html, '<a href="/kwf/kwctest/Kwf_Component_ComponentLinkModifiers_Root/page1"><span>page1</span></a><span class="appendText">foobar</span>');
    }
}
