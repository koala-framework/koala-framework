<?php
class Kwf_Component_Cache_RendererDynamicUseViewCache_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_RendererDynamicUseViewCache_Root');
        $this->_root->setFilename(false);
        Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial::setIds(array(1,2,3));
        Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_Component::$getPartialVarsCalled = 0;
        Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial::$getIdsCalled = 0;
    }

    public function testContent()
    {
        $c = $this->_root->getComponentById('root_page1');
        $html = $c->render();
        $this->assertEquals("<page1>\n123\n</page1>\n", $html);
        $this->assertEquals(3, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_Component::$getPartialVarsCalled);
        $this->assertEquals(1, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial::$getIdsCalled);

        $html = $c->render(); //re-render, everything should be cached
        $this->assertEquals("<page1>\n123\n</page1>\n", $html);
        $this->assertEquals(3, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_Component::$getPartialVarsCalled);
        $this->assertEquals(1, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial::$getIdsCalled);

        Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial::setIds(array(4,5));
        $html = $c->render();
        $this->assertContains('45', $html);
        $this->assertEquals(5, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_Component::$getPartialVarsCalled);
        $this->assertEquals(2, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial::$getIdsCalled);
    }

    public function testMasterContent()
    {
        $c = $this->_root->getComponentById('root_page1');
        $html = $c->render(true, true);
        $this->assertContains('123', $html);
        $this->assertEquals(3, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_Component::$getPartialVarsCalled);
        $this->assertEquals(1, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial::$getIdsCalled);

        $html = $c->render(true, true); //re-render, everything should be cached
        $this->assertContains("123", $html);
        $this->assertEquals(3, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_Component::$getPartialVarsCalled);
        $this->assertEquals(1, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial::$getIdsCalled);

        Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial::setIds(array(4,5));
        $html = $c->render(true, true);
        $this->assertContains('45', $html);
        $this->assertEquals(5, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_Component::$getPartialVarsCalled);
        $this->assertEquals(2, Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial::$getIdsCalled);
    }

    public function testMasterFirstRenderNoCache() //problematic to fill fullPage cache
    {
        $c = $this->_root->getComponentById('root_page1');
        Kwf_Component_Cache_RendererDynamicUseViewCache_Page1_TestPartial::setIds(array(4,5));
        $html = $c->render(true, true);
        $this->assertContains('45', $html);
        $this->assertNotContains('123', $html);
    }
}