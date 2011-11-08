<?php
/**
 * @group Kwc_Menu
 */
class Kwf_Component_Cache_Menu_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_Menu_Root_Component');
        /*
        root
          -menu
          _1
            -menu
            _2
              -menu
              _3
                -menu
          _4
            -menu
          _5 (invisible)
            -menu
         */
    }

    public function testChangePage()
    {
        $page = $this->_root->getComponentById(1);

        $html = $page->render(true, true);
        $this->assertEquals(2, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'f1'));
        $this->assertEquals(2, substr_count($html, 'f4'));
        Kwf_Component_Data_Root::reset();

        $row = $this->_root->getGenerator('page')->getModel()->getRow(1);
        $row->name = 'g1';
        $row->filename = 'g1';
        $row->save();
        $this->_process();

        $html = $page->render(true, true);

        $this->assertEquals(2, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'g1'));
        $this->assertEquals(2, substr_count($html, 'f4'));
    }

    public function testAddPage()
    {
        $page = $this->_root->getComponentById(1);
        $page->render(true, true);

        Kwf_Component_Data_Root::reset();

        $row = $this->_root->getGenerator('page')->getModel()->createRow(array(
            'pos'=>3, 'visible'=>true, 'name'=>'f5', 'filename' => 'f5',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null
        ));
        $row->save();
        $this->_process();
        $html = $page->render(true, true);
        $this->assertEquals(3, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'f1'));
        $this->assertEquals(2, substr_count($html, 'f4'));
        $this->assertEquals(2, substr_count($html, 'f5'));
    }

    public function testChangeParent()
    {
        $page = $this->_root->getComponentById(3);
        $model = $this->_root->getGenerator('page')->getModel();
        $m = Kwf_Component_Cache::getInstance()->getModel();

        // ist ein ParentContent-Komponente, zeigt 1-menu an
        $html = $page->render(true, true);
        $this->assertEquals(2, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'f1'));
        $this->assertEquals(2, substr_count($html, 'f4'));

        Kwf_Component_Data_Root::reset();

        // Um sicherzugehen, dass 1-menu nicht mehr existiert, 1 und 2 löschen
        $row = $model->getRow(3);
        $row->parent_id = 'root';
        $row->save();
        $row = $model->getRow(2);
        $row->delete();
        $row = $model->getRow(1);
        $row->delete();
        $this->_process();

        $html = $page->render(true, true);
        $this->assertEquals(2, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'f3'));
        $this->assertEquals(2, substr_count($html, 'f4'));
    }

    public function testRemovePage()
    {
        $page = $this->_root->getComponentById(1);
        $page->render(true, true);

        Kwf_Component_Data_Root::reset();

        $this->_root->getGenerator('page')->getModel()->getRow(4)->delete();

        $this->_process();
        $html = $page->render(true, true);
        $this->assertEquals(1, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'f1'));
        $this->assertEquals(0, substr_count($html, 'f4'));
    }

    public function testMakeInvisiblePage()
    {
        $page = $this->_root->getComponentById(1);
        $page->render(true, true);

        Kwf_Component_Data_Root::reset();

        $row = $this->_root->getGenerator('page')->getModel()->getRow(4);
        $row->visible = false;
        $row->save();

        $this->_process();
        $html = $page->render(true, true);
        $this->assertEquals(1, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'f1'));
        $this->assertEquals(0, substr_count($html, 'f4'));
    }

    public function testMakeVisiblePage()
    {
        $page = $this->_root->getComponentById(1);
        $page->render(true, true);

        Kwf_Component_Data_Root::reset();

        $row = $this->_root->getGenerator('page')->getModel()->getRow(5);
        $row->visible = true;
        $row->save();

        $this->_process();
        $html = $page->render(true, true);
        $this->assertEquals(3, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'f1'));
        $this->assertEquals(2, substr_count($html, 'f4'));
        $this->assertEquals(2, substr_count($html, 'f5'));
    }

    public function testPositionChange()
    {
        $page = $this->_root->getComponentById(1);
        $html = $page->render(true, true);
        $this->assertRegExp('#f1.*f4#s', $html);
        Kwf_Component_Data_Root::reset();

        $row = $this->_root->getGenerator('page')->getModel()->getRow(1);
        $row->pos = 5;
        $row->save();

        $this->_process();
        $html = $page->render(true, true);

        $this->assertRegExp('#f4.*f1#s', $html);
        $this->assertEquals(2, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'f1'));
        $this->assertEquals(2, substr_count($html, 'f4'));
    }
}
