<?php
/**
 * @group Component_Cache_Menu
 */
class Vps_Component_Cache_Menu_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_Menu_Root_Component');
    }

    public function testMenu()
    {
        $page = $this->_root->getComponentById(1);

        $html = $page->render(true, true);
        //d($html);
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
}
