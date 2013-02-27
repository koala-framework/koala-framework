<?php
/**
 * @group Kwc_Trl
 * @group Kwc_Menu

ansicht frontend:
http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_MenuCache_Root/de
http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_MenuCache_Root/de/home_de/test
http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_MenuCache_Root/en
http://kwf.kwf.niko.vivid/kwf/kwctest/Kwc_Trl_MenuCache_Root/en/home_en/test
 */
class Kwc_Trl_MenuCache_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_MenuCache_Root');
    }

    public function testComponentClasses()
    {
        $this->assertEquals($this->_root->getComponentById('root-mainMenu'), null);
        $this->assertEquals($this->_root->getComponentById('root-mainMenu-subMenu'), null);

        $this->assertEquals($this->_root->getComponentById('root-master-mainMenu')->componentClass, 'Kwc_Menu_OtherCategoryChild_Component.Kwc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('root-master-mainMenu-subMenu'), null);

        $this->assertEquals($this->_root->getComponentById('root-master-main-mainMenu')->componentClass, 'Kwc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('root-master-main-mainMenu-subMenu')->componentClass, 'Kwc_Basic_None_Component');

        $this->assertEquals($this->_root->getComponentById('1-mainMenu')->componentClass, 'Kwc_Menu_ParentMenu_Component.Kwc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('1-mainMenu-subMenu')->componentClass, 'Kwc_Trl_MenuCache_MainMenu_SubMenu_Component');

        $this->assertEquals($this->_root->getComponentById('2-mainMenu')->componentClass, 'Kwc_Menu_ParentMenu_Component.Kwc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('2-mainMenu-subMenu')->componentClass, 'Kwc_Menu_ParentMenu_Component.Kwc_Trl_MenuCache_MainMenu_SubMenu_Component');

        $this->assertEquals($this->_root->getComponentById('4-mainMenu')->componentClass, 'Kwc_Menu_ParentContent_Component.Kwc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('4-mainMenu-subMenu'), null);
    }

    public function testComponentClassesBottom()
    {
        $this->assertEquals($this->_root->getComponentById('root-master-bottom-mainMenu')->componentClass, 'Kwc_Menu_OtherCategory_Component.Kwc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('root-master-bottom-mainMenu-subMenu'), null);

        $this->assertEquals($this->_root->getComponentById('5-mainMenu')->componentClass, 'Kwc_Menu_ParentContent_Component.Kwc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('5-mainMenu-subMenu'), null);

        $this->assertEquals($this->_root->getComponentById('6-mainMenu')->componentClass, 'Kwc_Menu_ParentContent_Component.Kwc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('6-mainMenu-subMenu'), null);
    }

    public function testMenuDeNameChanged()
    {
        $c = $this->_root->getComponentById('1-mainMenu');
        $html = $c->render();
        $this->assertContains('Home de</a>', $html);
        $this->assertContains('Test</a>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_PagesTestModel')
            ->getRow(2);
        $row->name = 'Testx';
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Home de</a>', $html);
        $this->assertContains('Testx</a>', $html);
    }

    public function testMenuEnNameChanged()
    {
        $c = $this->_root->getComponentById('root-en-main_1-mainMenu');

        $html = $c->render();
        $this->assertContains('Home en</a>', $html);
        $this->assertContains('Test</a>', $html);
        $this->assertContains('Test2 en</a>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_2');
        $row->name = 'Testxen';
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Home en</a>', $html);
        $this->assertContains('Testxen</a>', $html);
    }

    public function testMenuOtherCategoryDeNameChanged()
    {
        $c = $this->_root->getComponentById('root-master-bottom-mainMenu');
        $html = $c->render();
        $this->assertContains('Home de</a>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_PagesTestModel')
            ->getRow(1);
        $row->name = 'Homex de';
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Homex de</a>', $html);
    }

    public function testMenuOtherCategoryEnNameChanged()
    {
        $c = $this->_root->getComponentById('root-en-bottom-mainMenu');
        $html = $c->render();
        $this->assertContains('Home en</a>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_1');
        $row->name = 'Homex en';
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Homex en</a>', $html);
    }

    public function testMenuOtherCategoryEnShow()
    {
        $c = $this->_root->getComponentById('root-en-bottom-mainMenu');
        $html = $c->render();
        $this->assertNotContains('Test7 en</a>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_7');
        $row->visible = true;
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Test7 en</a>', $html);
    }

    public function testMenuOtherCategoryEnHide()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_7');
        $row->visible = true;
        $row->save();

        $c = $this->_root->getComponentById('root-en-bottom-mainMenu');
        $html = $c->render();
        $this->assertContains('Test7 en</a>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_7');
        $row->visible = false;
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertNotContains('Test7 en</a>', $html);
    }

    public function testParentMenuEnShow()
    {
        $c = $this->_root->getComponentById('root-en-main_1-mainMenu');
        $html = $c->render();
        $this->assertNotContains('Test7 en</a>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_7');
        $row->visible = true;
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Test7 en</a>', $html);
    }

    public function testParentMenuEnHide()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_7');
        $row->visible = true;
        $row->save();

        $c = $this->_root->getComponentById('root-en-main_1-mainMenu');

        $html = $c->render();
        $this->assertContains('Test7 en</a>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_7');
        $row->visible = false;
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertNotContains('Test7 en</a>', $html);
    }

    public function testMenuEnShow()
    {
        $c = $this->_root->getComponentById('root-en-main-mainMenu');
        $html = $c->render();
        $this->assertNotContains('Test7 en</a>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_7');
        $row->visible = true;
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Test7 en</a>', $html);
    }

    public function testMenuEnHide()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_7');
        $row->visible = true;
        $row->save();

        $c = $this->_root->getComponentById('root-en-main-mainMenu');

        $html = $c->render();
        $this->assertContains('Test7 en</a>', $html);

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_7');
        $row->visible = false;
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertNotContains('Test7 en</a>', $html);
    }
}
