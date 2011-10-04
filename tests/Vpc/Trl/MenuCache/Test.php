<?php
/**
 * @group Vpc_Trl
 * @group Vpc_Trl_Menu
 * @group Vpc_Trl_MenuCache

ansicht frontend:
http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_MenuCache_Root/de
http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_MenuCache_Root/de/home_de/test
http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_MenuCache_Root/en
http://vps.vps.niko.vivid/vps/vpctest/Vpc_Trl_MenuCache_Root/en/home_en/test
 */
class Vpc_Trl_MenuCache_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_MenuCache_Root');
    }

    public function testComponentClasses()
    {
        $this->assertEquals($this->_root->getComponentById('root-mainMenu')->componentClass, 'Vpc_Basic_Empty_Component');
        $this->assertEquals($this->_root->getComponentById('root-mainMenu-subMenu'), null);

        $this->assertEquals($this->_root->getComponentById('root-master-mainMenu')->componentClass, 'Vpc_Basic_Empty_Component');
        $this->assertEquals($this->_root->getComponentById('root-master-mainMenu-subMenu'), null);

        $this->assertEquals($this->_root->getComponentById('root-master-main-mainMenu')->componentClass, 'Vpc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('root-master-main-mainMenu-subMenu')->componentClass, 'Vpc_Basic_Empty_Component');

        $this->assertEquals($this->_root->getComponentById('1-mainMenu')->componentClass, 'Vpc_Menu_ParentMenu_Component.Vpc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('1-mainMenu-subMenu')->componentClass, 'Vpc_Trl_MenuCache_MainMenu_SubMenu_Component');

        $this->assertEquals($this->_root->getComponentById('2-mainMenu')->componentClass, 'Vpc_Menu_ParentMenu_Component.Vpc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('2-mainMenu-subMenu')->componentClass, 'Vpc_Menu_ParentMenu_Component.Vpc_Trl_MenuCache_MainMenu_SubMenu_Component');

        $this->assertEquals($this->_root->getComponentById('4-mainMenu')->componentClass, 'Vpc_Basic_ParentContent_Component');
        $this->assertEquals($this->_root->getComponentById('4-mainMenu-subMenu'), null);
    }

    public function testComponentClassesBottom()
    {
        $this->assertEquals($this->_root->getComponentById('root-master-bottom-mainMenu')->componentClass, 'Vpc_Menu_OtherCategory_Component.Vpc_Trl_MenuCache_MainMenu_Component');
        $this->assertEquals($this->_root->getComponentById('root-master-bottom-mainMenu-subMenu'), null);

        $this->assertEquals($this->_root->getComponentById('5-mainMenu')->componentClass, 'Vpc_Basic_ParentContent_Component');
        $this->assertEquals($this->_root->getComponentById('5-mainMenu-subMenu'), null);

        $this->assertEquals($this->_root->getComponentById('6-mainMenu')->componentClass, 'Vpc_Basic_ParentContent_Component');
        $this->assertEquals($this->_root->getComponentById('6-mainMenu-subMenu'), null);
    }

    public function testMenuDe()
    {
        $c = $this->_root->getComponentById('1-mainMenu');
        $html = $c->render();
        $this->assertContains('Home de</a>', $html);
        $this->assertContains('Test</a>', $html);

        $row = Vps_Model_Abstract::getInstance('Vpc_Trl_MenuCache_Category_PagesTestModel')
            ->getRow(2);
        $row->name = 'Testx';
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Home de</a>', $html);
        $this->assertContains('Testx</a>', $html);
    }

    public function testMenuEn()
    {
        $c = $this->_root->getComponentById('root-en-main_1-mainMenu');

        $html = $c->render();
        $this->assertContains('Home en</a>', $html);
        $this->assertContains('Test</a>', $html);
        $this->assertContains('Test2 en</a>', $html);

        $row = Vps_Model_Abstract::getInstance('Vpc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_2');
        $row->name = 'Testxen';
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Home en</a>', $html);
        $this->assertContains('Testxen</a>', $html);
    }

    public function testMenuOtherCategoryDe()
    {
        $c = $this->_root->getComponentById('root-master-bottom-mainMenu');
        $html = $c->render();
        $this->assertContains('Home de</a>', $html);

        $row = Vps_Model_Abstract::getInstance('Vpc_Trl_MenuCache_Category_PagesTestModel')
            ->getRow(1);
        $row->name = 'Homex de';
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Homex de</a>', $html);
    }

    public function testMenuOtherCategoryEn()
    {
        $c = $this->_root->getComponentById('root-en-bottom-mainMenu');
        $html = $c->render();
        $this->assertContains('Home en</a>', $html);

        $row = Vps_Model_Abstract::getInstance('Vpc_Trl_MenuCache_Category_Trl_PagesTrlTestModel')
            ->getRow('root-en-main_1');
        $row->name = 'Homex en';
        $row->save();

        $this->_process();

        $html = $c->render();
        $this->assertContains('Homex en</a>', $html);
    }
}
