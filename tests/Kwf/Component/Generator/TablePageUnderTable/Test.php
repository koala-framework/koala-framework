<?php
/**
 * @group Generator_TablePageUnderTable
 */
class Kwf_Component_Generator_TablePageUnderTable_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Generator_TablePageUnderTable_Root');
/*
root
  page1 (page)
    1 (no page)
      1 (page) -> needs unique url!
      2 (page) -> needs unique url!
    2 (no page)
      3 (page) -> needs unique url!
    3 (no page)
*/
    }

    public function testUrl()
    {
        $this->_root->setFilename(null);

        $c = $this->_root->getComponentById('root_page1');
        $this->assertEquals($c->url, '/page1');

        $c = $this->_root->getComponentById('root_page1-1');
        $this->assertEquals($c->url, '/page1');

        $c = $this->_root->getComponentById('root_page1-1_1');
        $this->assertEquals($c->url, '/page1/1-asdf');

        $c = $this->_root->getComponentById('root_page1-1_2');
        $this->assertEquals($c->url, '/page1/2-asdf');

        $c = $this->_root->getComponentById('root_page1-2_3');
        $this->assertEquals($c->url, '/page1/3-asdf');
    }

    public function testResolveUrl()
    {
        $this->_root->setFilename(null);

        $c = $this->_root->getChildPageByPath('page1/1-asdf', null);
        $this->assertTrue(!!$c);
        $this->assertEquals($c->componentId, 'root_page1-1_1');

        $c = $this->_root->getChildPageByPath('page1/2-asdf', null);
        $this->assertTrue(!!$c);
        $this->assertEquals($c->componentId, 'root_page1-1_2');

        $c = $this->_root->getChildPageByPath('page1/3-asdf', null);
        $this->assertTrue(!!$c);
        $this->assertEquals($c->componentId, 'root_page1-2_3');
    }
}
