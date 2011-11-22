<?php
/**
 * @group Kwc_Menu
 */
class Kwf_Component_Cache_MenuHasContent_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_MenuHasContent_Root');
        Kwf_Component_Cache_MenuHasContent_Root_Events::$hasContentChanged = array();
        /*
        root
          -menuMain
          -menuTop
          -top
            -menuMain
            -menuTop
            2 (invisible)
              -menuMain
              -menuTop
          -main
            -menuMain
            -menuTop
            1
              -menuMain
              -menuTop
              3
                -menuMain
                -menuTop
                4
                  -menuMain
                  -menuTop
         */
    }

    public function testAddPageToTop()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuHasContent_Category_PagesModel');
        $row = $m->createRow(array(
            'pos'=>3, 'visible'=>true, 'name'=>'f5', 'filename' => 'f5',
                  'parent_id'=>'root-top', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null
        ));
        $row->save();
        $this->_process();

        $ids = Kwf_Component_Cache_MenuHasContent_Root_Events::$hasContentChanged;
        sort($ids);
        $this->assertEquals(array('root-main-menuTop', 'root-top-menuTop'), $ids);
    }

    public function testAddPageToMain()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuHasContent_Category_PagesModel');
        $row = $m->createRow(array(
            'pos'=>3, 'visible'=>true, 'name'=>'f5', 'filename' => 'f5',
                  'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null
        ));
        $row->save();
        $this->_process();

        $ids = Kwf_Component_Cache_MenuHasContent_Root_Events::$hasContentChanged;
        $this->assertEquals(array(), $ids);
    }

    public function testMakePageVisibleFromTop()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuHasContent_Category_PagesModel');
        $row = $m->getRow(2);
        $row->visible = true;
        $row->save();
        $this->_process();

        $ids = Kwf_Component_Cache_MenuHasContent_Root_Events::$hasContentChanged;
        sort($ids);
        $this->assertEquals(array('root-main-menuTop', 'root-top-menuTop'), $ids);
    }

    public function testMakePageInvisibleFromMain()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuHasContent_Category_PagesModel');
        $row = $m->getRow(1);
        $row->visible = false;
        $row->save();
        $this->_process();

        $ids = Kwf_Component_Cache_MenuHasContent_Root_Events::$hasContentChanged;
        sort($ids);
        $this->assertEquals(array('root-main-menuMain', 'root-top-menuMain'), $ids);
    }

    public function testRemovePageFromMain()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuHasContent_Category_PagesModel');
        $m->getRow(4)->delete();
        $m->getRow(3)->delete();
        $m->getRow(1)->delete();
        $this->_process();

        $ids = Kwf_Component_Cache_MenuHasContent_Root_Events::$hasContentChanged;
        sort($ids);
        $this->assertEquals(array('root-main-menuMain', 'root-top-menuMain'), $ids);
    }
}
