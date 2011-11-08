<?php
/**
 * @group Kwc_Menu
 */
class Kwf_Component_Cache_Menu_TestAlternativeComponent extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_Menu_Root3_Component');
        /*
      root (menuCategory)
        root-menu1 (menu) Kwc_Menu_Component, level root, including subMenu
          root-menu1-subMenu (empty)
          1-menu1 (parentMenu)
            1-menu1-subMenu (menu)
            2-menu1 (parentMenu, because of submenu)
              2-menu1-subMenu (parentMenu)
              3-menu1 (parentContent)

        root-menu2 (empty) Kwc_Menu_Component, level 2
          1-menu2 (menu)
            2-menu2 (parentMenu)
              3-menu2 (parentContent)

        root-menu3 (menu) Kwc_Menu_Expanded_Component, level 'root'
          1-menu3 (parentMenu)
            2-menu3 (parentMenu)
              3-menu3 (parentContent)
        */
    }

    public function testMenu1()
    {
        $menu = $this->_root->getComponentById('root-menu1');
        $this->assertEquals('Kwf_Component_Cache_Menu_Root3_Menu1_Component', $menu->componentClass);
        $menu = $this->_root->getComponentById('root-menu1-subMenu');
        $this->assertEquals('Kwc_Basic_Empty_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById('1-menu1');
        $this->assertEquals('Kwc_Menu_ParentMenu_Component.Kwf_Component_Cache_Menu_Root3_Menu1_Component', $menu->componentClass);
        $menu = $this->_root->getComponentById('1-menu1-subMenu');
        $this->assertEquals('Kwf_Component_Cache_Menu_Root3_Menu1_Sub_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById('2-menu1');
        $this->assertEquals('Kwc_Menu_ParentMenu_Component.Kwf_Component_Cache_Menu_Root3_Menu1_Component', $menu->componentClass);
        $menu = $this->_root->getComponentById('2-menu1-subMenu');
        $this->assertEquals('Kwc_Menu_ParentMenu_Component.Kwf_Component_Cache_Menu_Root3_Menu1_Sub_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById('3-menu1');
        $this->assertEquals('Kwc_Menu_ParentContent_Component.Kwf_Component_Cache_Menu_Root3_Menu1_Component', $menu->componentClass);
        $this->assertNull($this->_root->getComponentById('3-menu1-subMenu'));
    }

    public function testMenu2()
    {
        $menu = $this->_root->getComponentById('root-menu2');
        $this->assertEquals('Kwc_Basic_Empty_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById('1-menu2');
        $this->assertEquals('Kwf_Component_Cache_Menu_Root3_Menu2_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById('2-menu2');
        $this->assertEquals('Kwc_Menu_ParentMenu_Component.Kwf_Component_Cache_Menu_Root3_Menu2_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById('3-menu2');
        $this->assertEquals('Kwc_Menu_ParentContent_Component.Kwf_Component_Cache_Menu_Root3_Menu2_Component', $menu->componentClass);
    }

    public function testMenu3()
    {
        $menu = $this->_root->getComponentById('root-menu3');
        $this->assertEquals('Kwf_Component_Cache_Menu_Root3_Menu3_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById('1-menu3');
        $this->assertEquals('Kwc_Menu_ParentMenu_Component.Kwf_Component_Cache_Menu_Root3_Menu3_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById('2-menu3');
        $this->assertEquals('Kwc_Menu_ParentMenu_Component.Kwf_Component_Cache_Menu_Root3_Menu3_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById('3-menu3');
        $this->assertEquals('Kwc_Menu_ParentContent_Component.Kwf_Component_Cache_Menu_Root3_Menu3_Component', $menu->componentClass);
    }
}
