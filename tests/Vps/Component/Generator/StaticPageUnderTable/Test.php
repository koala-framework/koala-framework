<?php
/**
 * @group Generator_StaticPageUnderTable
 */
class Vps_Component_Generator_StaticPageUnderTable_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Generator_StaticPageUnderTable_Root');
/*
root
  page1 (page)
    1 (no page)
      page (page) -> needs unique url!
    2 (no page)
      page (page)
*/
    }

    public function testUrl()
    {
        $this->_root->setFilename(null);

        $c = $this->_root->getComponentById('root_page1');
        $this->assertEquals($c->url, '/page1');

        $c = $this->_root->getComponentById('root_page1-1');
        $this->assertEquals($c->url, '/page1');

        $c = $this->_root->getComponentById('root_page1-1_page');
        $this->assertEquals($c->url, '/page1/1:page');
    }

    public function testResolveUrl()
    {
        $this->_root->setFilename(null);

        $c = $this->_root->getChildPageByPath('page1/1:page', null);
        $this->assertTrue(!!$c);
        $this->assertEquals($c->componentId, 'root_page1-1_page');
    }
}
