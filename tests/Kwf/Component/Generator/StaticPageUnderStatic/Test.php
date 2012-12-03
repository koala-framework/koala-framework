<?php
/**
 * @group Generator_StaticPageUnderStatic
 */
class Kwf_Component_Generator_StaticPageUnderStatic_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_StaticPageUnderStatic_Root');
/*
root
  page1 (page)
    component1 (no page)
      page (page) -> /component1/page
    component2 (no page) // augment url off
      component3 (no page)
        page (page) -> /component3/page
*/
    }

    public function testUrl()
    {
        $this->_root->setFilename(null);

        $c = $this->_root->getComponentById('root-component1_page');
        $this->assertEquals('/component1:page', $c->url);

        $c = $this->_root->getComponentById('root-component2-component3_page');
        $this->assertEquals('/component3:page', $c->url);
    }

    public function testResolveUrl()
    {
        $this->_root->setFilename(null);

        $c = $this->_root->getChildPageByPath('component1:page', null);
        $this->assertTrue(!!$c);
        $this->assertEquals('root-component1_page', $c->componentId);

        $c = $this->_root->getChildPageByPath('component3:page', null);
        $this->assertTrue(!!$c);
        $this->assertEquals('root-component2-component3_page', $c->componentId);
    }
}
