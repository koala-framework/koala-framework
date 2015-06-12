<?php
/**
 * @group Kwc_Menu
 */
class Kwc_Menu_ClearCacheExpanded_ChangeParentTest extends Kwc_TestAbstract
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
    public function testMainMoveFromChild3($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test8"', $html);
        $this->assertContains('"/test5"', $html);
        $this->assertContains('"/test5/test6"', $html);

        $row = $m->getRow(4);
        $row->parent_id = '1';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test4"', $html);
    }

    /**
     * @dataProvider mainMenuComponentIds
     */
    public function testMainMoveFromChild2($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test8"', $html);
        $this->assertContains('"/test5"', $html);
        $this->assertContains('"/test5/test6"', $html);

        $row = $m->getRow(4);
        $row->parent_id = 'root-main';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test4"', $html);
    }

    /**
     * @dataProvider mainMenuComponentIds
     */
    public function testMainMoveFromChild1($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test8"', $html);
        $this->assertContains('"/test5"', $html);
        $this->assertContains('"/test5/test6"', $html);

        $row = $m->getRow(3);
        $row->parent_id = 'root-main';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test3"', $html);
        $this->assertNotContains('"/test1/test3"', $html);
    }

    /**
     * @dataProvider mainMenuComponentIds
     */
    public function testMainMoveToOtherCategory1($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test8"', $html);
        $this->assertContains('"/test5"', $html);
        $this->assertContains('"/test5/test6"', $html);

        $row = $m->getRow(1);
        $row->parent_id = 'root-bottom';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertNotContains('/test1"', $html);
        $this->assertNotContains('/test1/test3"', $html);
        $this->assertNotContains('/test1/test8"', $html);
    }

    /**
     * @dataProvider mainMenuComponentIds
     */
    public function testMainMoveToOtherCategory2($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test8"', $html);
        $this->assertContains('"/test5"', $html);
        $this->assertContains('"/test5/test6"', $html);

        $row = $m->getRow(3);
        $row->parent_id = 'root-bottom';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertNotContains('/test1/test3"', $html);
    }

    /**
     * @dataProvider mainMenuComponentIds
     */
    public function testMainMoveFromOtherCategory1($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test8"', $html);
        $this->assertContains('"/test5"', $html);
        $this->assertContains('"/test5/test6"', $html);

        $row = $m->getRow(2);
        $row->parent_id = 'root-main';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test2"', $html);
    }

    /**
     * @dataProvider mainMenuComponentIds
     */
    public function testMainMoveFromOtherCategory2($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test8"', $html);
        $this->assertContains('"/test5"', $html);
        $this->assertContains('"/test5/test6"', $html);

        $row = $m->getRow(2);
        $row->parent_id = '1';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test2"', $html);
    }

    public function subMenuComponentIds()
    {
        return array(
            array('1-menuSub'),
            array('3-menuSub'),
            array('4-menuSub'),
            array('10-menuSub'),
            array('11-menuSub')
        );
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveFromOtherCategory1($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test3/test4"', $html);

        $row = $m->getRow(2);
        $row->parent_id = '1';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test2"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveFromOtherCategory2($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test3/test4"', $html);

        $row = $m->getRow(2);
        $row->parent_id = '3';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3/test2"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveToOtherCategory1($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);

        $row = $m->getRow(3);
        $row->parent_id = 'root-bottom';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertNotContains('/test1/test3"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveToOtherCategory2($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3/test4"', $html);

        $row = $m->getRow(4);
        $row->parent_id = 'root-bottom';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertNotContains('/test1/test3/test4"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveFromChild1($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);

        $row = $m->getRow(4);
        $row->parent_id = '1';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test4"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveFromChild2($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3/test4"', $html);

        $row = $m->getRow(10);
        $row->parent_id = '3';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3/test10"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveFromOtherSub11($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);

        $row = $m->getRow(7);
        $row->parent_id = '3';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);

        $html = $this->_root->getComponentById('7-menuSub')->render();
        $this->assertContains('"/test1/test3"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveFromOtherSub12($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);

        $row = $m->getRow(7);
        $row->parent_id = '4';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3/test4"', $html);

        $html = $this->_root->getComponentById('7-menuSub')->render();
        $this->assertContains('"/test1/test3/test4"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveFromOtherSub21($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);

        $row = $m->getRow(6);
        $row->parent_id = '3';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);

        $html = $this->_root->getComponentById('6-menuSub')->render();
        $this->assertContains('"/test1/test3"', $html);

        $html = $this->_root->getComponentById('7-menuSub')->render();
        $this->assertContains('"/test1/test3"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveFromOtherSub22($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);

        $row = $m->getRow(6);
        $row->parent_id = '4';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3/test4"', $html);

        $html = $this->_root->getComponentById('6-menuSub')->render();
        $this->assertContains('"/test1/test3/test4"', $html);

        $html = $this->_root->getComponentById('7-menuSub')->render();
        $this->assertContains('"/test1/test3/test4"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveFromOtherSub3($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCacheExpanded_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);

        $row = $m->getRow(6);
        $row->parent_id = '1';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test6"', $html);

        $html = $this->_root->getComponentById('6-menuSub')->render();
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test6"', $html);

        $html = $this->_root->getComponentById('7-menuSub')->render();
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test6"', $html);
    }
}
