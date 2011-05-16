<?php
/**
 * @group Vpc_Menu
 * @group slow
 */
// /vps/componentedittest/Vpc_Menu_Root/Vpc_Menu_Menu_Component?componentId=root-menu
// /vps/componentedittest/Vpc_Menu_Root/Vpc_Menu_Menu_Component?componentId=1-menu-subMenu
// /vps/componentedittest/Vpc_Menu_Root/Vpc_Menu_LevelMenu_Component?componentId=3-levelmenu
class Vpc_Menu_SeleniumTest extends Vps_Test_SeleniumTestCase
{
    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Menu_Root');
        parent::setUp();
    }

    public function testMenu()
    {
        $this->openVpcEdit('Vpc_Menu_Menu_Component', 'root-menu');
        $this->waitForConnections();
        $this->assertTextPresent('Seite 1');
        $this->assertTextPresent('Seite 2');
    }

    public function testSubMenu()
    {
        $this->openVpcEdit('Vpc_Menu_Menu_Component', '1-menu-subMenu');
        $this->waitForConnections();
        $this->assertTextPresent('Seite 3');
    }

    public function testExpandedMenu()
    {
        $this->openVpcEdit('Vpc_Menu_LevelMenu_Component', '3-levelmenu');
        $this->waitForConnections();
        $this->assertTextPresent('Seite 4');
        $this->assertTextPresent('Seite 5');
    }
}
