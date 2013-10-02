<?php
/**
 * @group Kwc_Basic_LinkTagFirstChildPage
 **/
class Kwc_Basic_LinkTagFirstChildPage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Basic_LinkTagFirstChildPage_Root');
        $this->_root->setFilename(null);
    }

    public function testUrlAndRel()
    {
        $c = $this->_root->getComponentById(1500);
        $this->assertEquals('/foo1/bar1', $c->url);
        $this->assertEquals('', $c->rel);

        $c = $this->_root->getComponentById(1502);
        $this->assertEquals('/foo2/bar2/baz2', $c->url);
        $this->assertEquals('', $c->rel);

    }

    public function testEmpty()
    {
        //ist das das gewünscht verhalten?
        $c = $this->_root->getComponentById(1505);
        $this->assertEquals('', $c->url);
        $this->assertEquals('', $c->rel);
    }

    public function testMenuCacheOnMovePage()
    {
        $menu = $this->_root->getChildComponent('-menu');

        $html = $menu->render();
        $this->assertEquals(2, substr_count($html, 'href='));
        $this->assertTrue(strpos($html, 'href="/foo2/bar2/baz2"') > 0);
        $this->assertTrue(strpos($html, 'href="/foo1/bar1"') > 0);

        $model = Kwf_Model_Abstract::getInstance('Kwc_Basic_LinkTagFirstChildPage_PagesModel');
        $row = $model->getRow(1504);
        $row->parent_id = 1505;
        $row->save();
        $this->_process();

        $html = $menu->render();
        $this->assertEquals(2, substr_count($html, 'href='));
        $this->assertTrue(strpos($html, 'href="/foo3/baz2"') > 0);
        $this->assertTrue(strpos($html, 'href="/foo1/bar1"') > 0);
    }
}
