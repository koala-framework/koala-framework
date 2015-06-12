<?php
/**
 * @group Kwc_Menu
 */
class Kwc_Menu_ClearCacheExpanded_ChangeVisibleTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Menu_ClearCacheExpanded_Root');
        $this->_root->setFilename('');
        /*
        root
          -menuMain
          -menuSub
          -main
            -menuMain
            -menuSub
            1
              -menuMain
              -menuSub
              3
                -menuMain
                -menuSub
                4
                  -menuMain
                  -menuSub
                  10
                    -menuMain
                    -menuSub
                    11
                      -menuMain
                      -menuSub
              8
                -menuMain
                -menuSub
                9
                  -menuMain
                  -menuSub
            5
              -menuMain
              -menuSub
              6
                -menuMain
                -menuSub
                7
                  -menuMain
                  -menuSub
          -bottom
            -menuMain
            -menuSub
            2
              -menuMain
              -menuSub
         */
    }

    public function mainMenuComponentIds()
    {
        return array(
            array('root-menuMain'),
            array('root-main-menuMain'),
            array('root-bottom-menuMain'),
            array('1-menuMain'),
            array('2-menuMain'),
            array('3-menuMain'),
            array('4-menuMain'),
            array('10-menuMain'),
            array('11-menuMain'),
        );
    }

    /**
     * @dataProvider mainMenuComponentIds
     */
    public function testMainHide1($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test5"', $html);
        $this->assertContains('"/test5/test6"', $html);

        $row = $m->getRow(5);
        $row->visible = false;
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertNotContains('"/test5"', $html);
        $this->assertNotContains('"/test5/test6"', $html);
    }

    /**
     * @dataProvider mainMenuComponentIds
     */
    public function testMainHide2($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test5"', $html);
        $this->assertContains('"/test5/test6"', $html);

        $row = $m->getRow(6);
        $row->visible = false;
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertNotContains('"/test5/test6"', $html);
    }


    public function subMenuComponentIds()
    {
        return array(
            array('1-menuSub'),
            array('3-menuSub'),
            array('4-menuSub'),
            array('10-menuSub'),
            array('11-menuSub'),
        );
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubHide1($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test3/test4"', $html);
        $this->assertContains('"/test1/test8"', $html);
        $this->assertContains('"/test1/test8/test9"', $html);

        $row = $m->getRow(8);
        $row->visible = false;
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertNotContains('"/test1/test8"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubHide2($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test3/test4"', $html);
        $this->assertContains('"/test1/test8"', $html);
        $this->assertContains('"/test1/test8/test9"', $html);

        $row = $m->getRow(9);
        $row->visible = false;
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertNotContains('"/test1/test3/test9"', $html);
    }

}
