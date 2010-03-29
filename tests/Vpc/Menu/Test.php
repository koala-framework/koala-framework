<?php
/**
 * @group Vpc_Menu
 */
class Vpc_Menu_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vpc_Menu_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testEditComponents()
    {
        /*
        So sieht die Struktur aus:
        root (menu)
          1 (menu-subMenu)
            3 (menulevel)
              4
          2 (menu-subMenu)
        */

        $components = Vps_Controller_Action_Component_PagesController::getMenuEditComponents(
            $this->_root
        );
        $this->assertEquals(1, count($components));
        $component = array_shift($components);
        $this->assertEquals('Vpc_Menu_Menu_Component', $component->componentClass);
        $this->assertEquals('root-menu', $component->componentId);

        $components = Vps_Controller_Action_Component_PagesController::getMenuEditComponents(
            $this->_root->getComponentById(1)
        );
        $this->assertEquals(1, count($components));
        $component = array_shift($components);
        $this->assertEquals('Vpc_Menu_Menu_Component', $component->componentClass);
        $this->assertEquals('1-menu-subMenu', $component->componentId);

        $components = Vps_Controller_Action_Component_PagesController::getMenuEditComponents(
            $this->_root->getComponentById(3)
        );
        $this->assertEquals(1, count($components));
        $component = array_shift($components);
        $this->assertEquals('Vpc_Menu_LevelMenu_Component', $component->componentClass);
        $this->assertEquals('3-levelmenu', $component->componentId);
    }
}
