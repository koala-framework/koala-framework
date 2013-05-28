<?php
/**
 * @group Cc
 */
class Kwc_Cc_PageTree_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Cc_PageTree_Root');
        Kwf_Component_Data_Root::getInstance()->setFilename('');
    }
/*
 1
   4
 2
 3
*/
    public function testChangePageTypeClearCache()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('2');
        $html = $c->render(true, false);
        $this->assertEquals('', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_2');
        $html = $c->render(true, false);
        $this->assertEquals('', $html);

        $r = Kwf_Model_Abstract::getInstance('Kwc_Cc_PageTree_Master_Category_PagesModel')->getRow(2);
        $r->component = 'test';
        $r->save();
        $this->_process();

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('2');
        $html = $c->render(true, false);
        $this->assertEquals('Test', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_2');
        $html = $c->render(true, false);
        $this->assertEquals('Test', $html);
    }

    public function testFoo()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_4');
        $this->assertNotNull($c);
    }

    public function testMenuCc()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main-mainMenu');
        $html = $c->render(true, false);
        //TODO: home url is apperently the same for slave and master
        $this->assertContains('<a href="/slave/2" rel="">Seite 2</a>', $html);
        $this->assertContains('<a href="/slave/3" rel="">Seite 3</a>', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_2-mainMenu'); //level 1
        $html = $c->render(true, false);
        $this->assertContains('<a href="/slave/2" rel="">Seite 2</a>', $html);
        $this->assertContains('<a href="/slave/3" rel="">Seite 3</a>', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_4-mainMenu'); //level 2
        $html = $c->render(true, false);
        $this->assertContains('<a href="/slave/2" rel="">Seite 2</a>', $html);
        $this->assertContains('<a href="/slave/3" rel="">Seite 3</a>', $html);
    }

    public function testBottomMenuCc()
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main-bottomMenu');
        $html = $c->render(true, false);
        $this->assertContains('<a href="/slave/5" rel="">Seite 5</a>', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_2-bottomMenu'); //level 1
        $html = $c->render(true, false);
        $this->assertContains('<a href="/slave/5" rel="">Seite 5</a>', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_4-bottomMenu'); //level 2
        $html = $c->render(true, false);
        $this->assertContains('<a href="/slave/5" rel="">Seite 5</a>', $html);
    }

    public function testMenuCcClearCacheNameAndFilenameChanged()
    {
        //cache menus
        Kwf_Component_Data_Root::getInstance()
            ->getComponentById('root-slave-main-mainMenu')->render(true, false);
        Kwf_Component_Data_Root::getInstance()
            ->getComponentById('root-slave-main_2-mainMenu')->render(true, false);
        Kwf_Component_Data_Root::getInstance()
            ->getComponentById('root-slave-main_4-mainMenu')->render(true, false);

        $r = Kwf_Model_Abstract::getInstance('Kwc_Cc_PageTree_Master_Category_PagesModel')->getRow(2);
        $r->name = 'Seite 2 xy';
        $r->save();

        $this->_process();


        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main-mainMenu');
        $html = $c->render(true, false);
        $this->assertContains('<a href="/slave/seite_2_xy" rel="">Seite 2 xy</a>', $html);
        $this->assertContains('<a href="/slave/3" rel="">Seite 3</a>', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_2-mainMenu'); //level 1
        $html = $c->render(true, false);
        $this->assertContains('<a href="/slave/seite_2_xy" rel="">Seite 2 xy</a>', $html);
        $this->assertContains('<a href="/slave/3" rel="">Seite 3</a>', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_4-mainMenu'); //level 2
        $html = $c->render(true, false);
        $this->assertContains('<a href="/slave/seite_2_xy" rel="">Seite 2 xy</a>', $html);
        $this->assertContains('<a href="/slave/3" rel="">Seite 3</a>', $html);
    }

    public function testMenuCcClearCacheParentChanged()
    {
        //cache menus
        Kwf_Component_Data_Root::getInstance()
            ->getComponentById('root-slave-main-mainMenu')->render(true, false);
        Kwf_Component_Data_Root::getInstance()
            ->getComponentById('root-slave-main_2-mainMenu')->render(true, false);
        Kwf_Component_Data_Root::getInstance()
            ->getComponentById('root-slave-main_4-mainMenu')->render(true, false);

        $r = Kwf_Model_Abstract::getInstance('Kwc_Cc_PageTree_Master_Category_PagesModel')->getRow(4);
        $r->parent_id = 'root-master-main';
        $r->pos = 4;
        $r->save();

        $this->_process();


        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main-mainMenu');
        $html = $c->render(true, false);

        $this->assertContains('<a href="/slave/seite_4" rel="">Seite 4</a>', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_2-mainMenu'); //level 1
        $html = $c->render(true, false);
        $this->assertContains('<a href="/slave/seite_4" rel="">Seite 4</a>', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_4-mainMenu'); //level 2
        $html = $c->render(true, false);
        $this->assertContains('<a href="/slave/seite_4" rel="">Seite 4</a>', $html);
    }

    public function testMenuCcClearCachePositionChanged()
    {
        //cache menus
        Kwf_Component_Data_Root::getInstance()
            ->getComponentById('root-slave-main-mainMenu')->render(true, false);
        Kwf_Component_Data_Root::getInstance()
            ->getComponentById('root-slave-main_2-mainMenu')->render(true, false);
        Kwf_Component_Data_Root::getInstance()
            ->getComponentById('root-slave-main_4-mainMenu')->render(true, false);

        $r = Kwf_Model_Abstract::getInstance('Kwc_Cc_PageTree_Master_Category_PagesModel')->getRow(2);
        $r->pos = 3;
        $r->save();

        $this->_process();


        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main-mainMenu');
        $html = $c->render(true, false);
        $this->assertRegExp('#<a href="/slave/3" rel="">Seite 3</a>.*<a href="/slave/seite_2" rel="">Seite 2</a>#s', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_2-mainMenu'); //level 1
        $html = $c->render(true, false);
        $this->assertRegExp('#<a href="/slave/3" rel="">Seite 3</a>.*<a href="/slave/seite_2" rel="">Seite 2</a>#s', $html);

        $c = Kwf_Component_Data_Root::getInstance()->getComponentById('root-slave-main_4-mainMenu'); //level 2
        $html = $c->render(true, false);
        $this->assertRegExp('#<a href="/slave/3" rel="">Seite 3</a>.*<a href="/slave/seite_2" rel="">Seite 2</a>#s', $html);
    }
}
