<?php
/**
 * @group Component_Cache_Menu
 */
class Vps_Component_Cache_Menu_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_Menu_Root_Component');
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
         */
    }

    public function testMenu()
    {
        $page = $this->_root->getComponentById(1);

        $html = $page->render(true, true);
        $this->assertEquals(2, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'f1'));
        $this->assertEquals(2, substr_count($html, 'f4'));

        $row = $this->_root->getGenerator('page')->getModel()->getRow(1);
        $row->name = 'g1';
        $row->filename = 'g1';
        $row->save();
        $this->_process();
        //d($html);
        $html = $page->render(true, true);
        $this->assertEquals(2, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'g1'));
        $this->assertEquals(2, substr_count($html, 'f4'));

        $row = $this->_root->getGenerator('page')->getModel()->createRow(array(
            'id'=>5, 'pos'=>3, 'visible'=>true, 'name'=>'f5', 'filename' => 'f5',
                  'parent_id'=>'root', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null
        ));
        $row->save();
        $this->_process();
        $html = $page->render(true, true);
        //d($html);
        $this->assertEquals(3, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'g1'));
        $this->assertEquals(2, substr_count($html, 'f4'));
        $this->assertEquals(2, substr_count($html, 'f5'));
    }

    public function testMovePage()
    {
        $page = $this->_root->getComponentById(3);
        $model = $this->_root->getGenerator('page')->getModel();
        $m = Vps_Component_Cache::getInstance()->getModel();

        // ist ein ParentContent-Komponente, zeigt 1-menu an
        $html = $page->render(true, true);
        $this->assertEquals(2, substr_count($html, '<li'));
        $this->assertEquals(2, substr_count($html, 'f1'));
        $this->assertEquals(2, substr_count($html, 'f4'));

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
}
