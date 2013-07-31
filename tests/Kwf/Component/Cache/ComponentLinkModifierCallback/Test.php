<?php
class Kwf_Component_Cache_ComponentLinkModifierCallback_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        Kwf_Component_Cache_ComponentLinkModifierCallback_LinkTargetPage_Component::$linkModifierContent = 'foo';
        parent::setUp('Kwf_Component_Cache_ComponentLinkModifierCallback_Root_Component');
    }

    public function testIt()
    {
        $c = $this->_root->getComponentById('root_page1');
        $html = $c->render();
        $this->assertContains('linkTarget</a>foo', $html);

        Kwf_Component_Cache_ComponentLinkModifierCallback_LinkTargetPage_Component::$linkModifierContent = 'bar';
        //callback is done dynamically, no need to clear any caches
        $html = $c->render();
        $this->assertContains('linkTarget</a>bar', $html);
    }

    public function testFullpage()
    {
        $c = $this->_root->getComponentById('root_page1');
        $html = $c->render(true, true);
        $this->assertContains('linkTarget</a>foo', $html);

        Kwf_Component_Cache_ComponentLinkModifierCallback_LinkTargetPage_Component::$linkModifierContent = 'bar';

        //callback is dynamic, must change without clearing cache
        $html = $c->render(true, true);
        $this->assertContains('linkTarget</a>bar', $html);
    }
}
