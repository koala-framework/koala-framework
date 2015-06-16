<?php
/**
 * @group Kwc_Menu
 */
class Kwc_Menu_ClearCache_ChangeParentTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Menu_ClearCache_Root');
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
              -menuSub (Menu)
              3
                -menuMain
                -menuSub (ParentMenu)
                4
                  -menuMain
                  -menuSub (ParentContent)
              8
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
              9
                -menuMain
                -menuSub
                10
                  -menuMain
                  -menuSub
         */
    }

    public function mainMoveFromOtherCategoryData()
    {
        return array(
            array('root-menuMain', null),
            array('root-main-menuMain', null),
            array('root-bottom-menuMain', null),
            array('1-menuMain', '/test1'),
            array('2-menuMain', '/test2'),
            array('3-menuMain', '/test1'),
            array('4-menuMain', '/test1'),
        );
    }

    /**
     * @dataProvider mainMoveFromOtherCategoryData
     */
    public function testMainMoveFromOtherCategory($menuComponentId, $currentUrl)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCache_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test5"', $html);

        $row = $m->getRow(2);
        $row->parent_id = 'root-main';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test5"', $html);
        $this->assertContains('"/test2"', $html);
        if ($currentUrl) {
            $this->assertRegExp('#<li class="[^"]*current[^"]*">\s*<a href="'.$currentUrl.'"#', $html);
        } else {
            $this->assertNotRegExp('#<li class="[^"]*current[^"]*">#', $html);
        }
    }

    public function mainMoveToOtherCategoryData()
    {
        return array(
            array('root-menuMain', null),
            array('root-main-menuMain', null),
            array('root-bottom-menuMain', null),
            array('1-menuMain', null),
            array('2-menuMain', null),
            array('3-menuMain', null),
            array('4-menuMain', null),
            array('5-menuMain', '/test5'),
            array('6-menuMain', '/test5'),
            array('7-menuMain', '/test5'),
        );
    }

    /**
     * @dataProvider mainMoveToOtherCategoryData
     */
    public function testMainMoveToOtherCategory($menuComponentId, $currentUrl)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCache_Category_PagesModel');
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1"', $html);
        $this->assertContains('"/test5"', $html);

        $row = $m->getRow(1);
        $row->parent_id = 'root-bottom';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertNotContains('/test1"', $html);
        $this->assertContains('"/test5"', $html);
        if ($currentUrl) {
            $this->assertRegExp('#<li class="[^"]*current[^"]*">\s*<a href="'.$currentUrl.'"#', $html);
        } else {
            $this->assertNotRegExp('#<li class="[^"]*current[^"]*">#', $html);
        }
    }

    public function mainMoveFromChildData()
    {
        $initialMenuComponentIds = array(
            'root-menuMain',
            'root-main-menuMain',
            'root-bottom-menuMain',
            '1-menuMain',
            '2-menuMain',
            '3-menuMain',
            '4-menuMain',
        );
        return array(
            array(3, 'root-main', $initialMenuComponentIds, array()),

            array(10, 'root-main', $initialMenuComponentIds, array('10-menuMain')),
            array(9, 'root-main', $initialMenuComponentIds, array('9-menuMain', '10-menuMain')),
            array(2, 'root-main', $initialMenuComponentIds, array('2-menuMain', '9-menuMain', '10-menuMain')),

            array(10, '1', $initialMenuComponentIds, array('10-menuMain')),
            array(9, '1', $initialMenuComponentIds, array('9-menuMain', '10-menuMain')),
            array(2, '1', $initialMenuComponentIds, array('2-menuMain', '9-menuMain', '10-menuMain')),

            array(10, '3', $initialMenuComponentIds, array('10-menuMain')),
            array(9, '3', $initialMenuComponentIds, array('9-menuMain', '10-menuMain')),
            array(2, '3', $initialMenuComponentIds, array('2-menuMain', '9-menuMain', '10-menuMain')),
        );
    }

    /**
     * @dataProvider mainMoveFromChildData
     */
    public function testMainMoveFromChild($sourceId, $targetId, $initialMenuComponentIds, $movedMenuComponentIds)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCache_Category_PagesModel');
        foreach ($initialMenuComponentIds as $i) {
            $html = $this->_root->getComponentById($i)->render();
            $this->assertContains('"/test1"', $html);
            $this->assertContains('"/test5"', $html);
        }
        foreach ($movedMenuComponentIds as $i) {
            $html = $this->_root->getComponentById($i)->render(); //just render to fill cache
        }

        $row = $m->getRow($sourceId);
        $row->parent_id = $targetId;
        $row->save();
        $this->_process();

        foreach (array_merge($initialMenuComponentIds, $movedMenuComponentIds) as $i) {
            $html = $this->_root->getComponentById($i)->render();
            $this->assertContains('"/test1"', $html);
            $this->assertContains('"/test5"', $html);
            if ($targetId == 'root-main') {
                $this->assertContains('"/test'.$sourceId.'"', $html);
            }
        }
    }


    public function subMenuComponentIds()
    {
        return array(
            array('1-menuSub'),
            array('3-menuSub'),
            array('4-menuSub'),
        );
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveFromOtherCategory($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCache_Category_PagesModel');
        $this->_root->getComponentById('5-menuSub')->render();
        $this->_root->getComponentById('6-menuSub')->render();
        $this->_root->getComponentById('7-menuSub')->render();
        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);

        $row = $m->getRow(2);
        $row->parent_id = '1';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById($menuComponentId)->render();
        $this->assertContains('"/test1/test3"', $html);
        $this->assertContains('"/test1/test2"', $html);
    }

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveToOtherCategory($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCache_Category_PagesModel');
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
    public function testSubMoveFromChild($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCache_Category_PagesModel');
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
    public function testSubMoveFromOtherSub3($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCache_Category_PagesModel');
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

    /**
     * @dataProvider subMenuComponentIds
     */
    public function testSubMoveFromOtherSub2($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCache_Category_PagesModel');
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
    public function testSubMoveFromOtherSub1($menuComponentId)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwc_Menu_ClearCache_Category_PagesModel');
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
}
