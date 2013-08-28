<?php
/**
 * @group Kwc_Menu
 */
class Kwf_Component_Cache_MenuStaticPage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_MenuStaticPage_Root');
        /*
        root
          -menu
          _1
            -menu
            -1
              _page
         */
    }

    public function testMenu()
    {
        $c = $this->_root->getComponentById('1-menu');
        $html = $c->render(true, false);
        $this->assertEquals(1, substr_count($html, '<li'));
        $this->assertContains('/test1/1:page"', $html);
    }

    public function testAddPage()
    {
        $c = $this->_root->getComponentById('1-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuStaticPage_Paragraphs_TestModel');
        $row = $m->createRow();
        $row->pos = 2;
        $row->visible = true;
        $row->component_id = '1';
        $row->component = 'test';
        $row->save();

        $this->_process();

        $html = $c->render(true, false);
        $this->assertEquals(2, substr_count($html, '<li'));
        $this->assertContains('/test1/1:page"', $html);
        $this->assertContains('/test1/3:page"', $html);
    }

    public function testRemovePage()
    {
        $c = $this->_root->getComponentById('1-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuStaticPage_Paragraphs_TestModel');
        $m->getRow(1)->delete();

        $this->_process();

        $c = $this->_root->getComponentById('1-menu');
        $html = $c->render(true, false);
        $this->assertEquals(0, substr_count($html, '<li'));
    }

    public function testChangeParentPage()
    {
        $c = $this->_root->getComponentById('1-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuStaticPage_PagesModel');
        $row = $m->getRow(2);
        $row->parent_id = 1;
        $row->pos = 2;
        $row->save();

        $this->_process();

        $c = $this->_root->getComponentById('1-menu');
        $html = $c->render(true, false);
        $this->assertEquals(3, substr_count($html, '<li'));
        $this->assertContains('/test1/1:page"', $html);
        $this->assertContains('/test1/test2"', $html);
        $this->assertContains('/test1/test2/2:page"', $html);
    }

    public function testChangeFilenamePage()
    {
        $c = $this->_root->getComponentById('1-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuStaticPage_PagesModel');
        $row = $m->getRow(1);
        $row->name = 'testx';
        $row->save();

        $this->_process();

        $c = $this->_root->getComponentById('1-menu');
        $html = $c->render(true, false);
        $this->assertEquals(1, substr_count($html, '<li'));
        $this->assertContains('/testx/1:page"', $html);
    }
}
