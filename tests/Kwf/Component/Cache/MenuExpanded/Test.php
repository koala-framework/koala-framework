<?php
/**
 * @group Kwc_Menu
 */
class Kwf_Component_Cache_MenuExpanded_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_MenuExpanded_Root');
        /*
        root
          -menu
          _1
            -menu
            _2
              -menu
              _3
                -menu
                _8
                  -menu
              _6
                -menu
              _7
                -menu
            _4
              -menu
          _5
            -menu
         */
    }

    public function testContent()
    {
        $c = $this->_root->getComponentById('root-menu');
        $html = $c->render(true, false);
        $this->assertEquals(4, substr_count($html, '<li'));
        $this->assertContains('/test1"', $html);
        $this->assertContains('/test1/test2"', $html);
        $this->assertContains('/test1/test4"', $html);
        $this->assertContains('/test5"', $html);
        $this->assertRegExp('#/test1".*/test1/test2".*/test1/test4".*/test5".*"#s', $html); //test order
    }

    public function testChangeFilenameLevel1()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->getRow(5);
        $row->name = 'testx';
        $row->save();
        $this->_process();

        $html = $c->render(true, false);
        $this->assertContains('/testx"', $html);
        $this->assertNotContains('/test5"', $html);
    }

    public function testChangeFilenameLevel2()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->getRow(2);
        $row->name = 'testx';
        $row->save();
        $this->_process();

        $html = $c->render(true, false);
        $this->assertContains('/test1/testx"', $html);
        $this->assertNotContains('/test2"', $html);
    }

    public function testChangePositionLevel1()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->getRow(1);
        $row->pos = 2;
        $row->save();
        $this->_process();

        $html = $c->render(true, false);

        $this->assertRegExp('#/test5".*/test1".*/test1/test2".*/test1/test4"#s', $html); //test order
    }

    public function testChangePositionLevel2()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->getRow(2);
        $row->pos = 2;
        $row->save();
        $this->_process();

        $html = $c->render(true, false);

        $this->assertRegExp('#/test1".*/test1/test4".*/test1/test2".*/test5"#s', $html); //test order
    }

    public function testChangeAddPageLevel1()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->createRow();
        $row->parent_id = 'root';
        $row->pos = 3;
        $row->visible = true;
        $row->name = $row->filename = 'testnew';
        $row->custom_filename = $row->is_home = $row->hide = false;
        $row->component = 'empty';
        $row->save();
        $this->_process();

        $html = $c->render(true, false);
        $this->assertEquals(5, substr_count($html, '<li'));
        $this->assertContains('/testnew"', $html);
    }

    public function testChangeAddPageLevel2()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->createRow();
        $row->parent_id = 1;
        $row->pos = 3;
        $row->visible = true;
        $row->name = $row->filename = 'testnew';
        $row->custom_filename = $row->is_home = $row->hide = false;
        $row->component = 'empty';
        $row->save();
        $this->_process();

        $html = $c->render(true, false);
        $this->assertEquals(5, substr_count($html, '<li'));
        $this->assertContains('/test1/testnew"', $html);
    }

    public function testChangeRemovePageLevel1()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->getRow(5);
        $row->delete();
        $this->_process();

        $html = $c->render(true, false);
        $this->assertEquals(3, substr_count($html, '<li'));
        $this->assertNotContains('/test5"', $html);
    }

    public function testChangeRemovePageLevel2()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->getRow(4);
        $row->delete();
        $this->_process();

        $html = $c->render(true, false);
        $this->assertEquals(3, substr_count($html, '<li'));
        $this->assertNotContains('/test4"', $html);
    }

    public function testChangeParentMoveToLevel1()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->getRow(7);
        $row->parent_id = 'root';
        $row->save();
        $this->_process();

        $html = $c->render(true, false);
        $this->assertEquals(5, substr_count($html, '<li'));
        $this->assertContains('/test7"', $html);
    }

    public function testChangeParentMoveFromLevel1()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->getRow(5);
        $row->parent_id = 3;
        $row->save();
        $this->_process();

        $html = $c->render(true, false);
        $this->assertEquals(3, substr_count($html, '<li'));
        $this->assertNotContains('/test5"', $html);
    }

    public function testChangeParentMoveToLevel2()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->getRow(3);
        $row->parent_id = 1;
        $row->save();
        $this->_process();

        $html = $c->render(true, false);
        $this->assertEquals(5, substr_count($html, '<li'));
        $this->assertContains('/test3"', $html);
    }

    public function testChangeParentMoveFromLevel2()
    {
        $c = $this->_root->getComponentById('root-menu');
        $c->render(true, false);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuExpanded_PagesModel');
        $row = $m->getRow(4);
        $row->parent_id = 2;
        $row->save();
        $this->_process();

        $html = $c->render(true, false);
        $this->assertEquals(3, substr_count($html, '<li'));
        $this->assertNotContains('/test4"', $html);
    }
}
