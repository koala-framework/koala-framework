<?php
/**
 * @group Component_Cache_Menu
 * @group Component_Cache_MenuAlternativeComponent
 */
class Vps_Component_Cache_Menu_TestAlternativeComponent extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_Menu_Root3_Component');
        /*
        root (menuCategory)
          _1 (menu1) Vpc_Menu_Component, level root, maxlevel 2
            _2 (menu1)
              _3 (menu1 -> parentMenu)
          _11 (menu2) Vpc_Menu_Component, level 2, maxlevel 2
            _12 (menu2)
              _13 (menu2 -> parentMenu)
          _21 (menu3) Vpc_Menu_Expanded_Component, level 'root'
            _22 (menu3)
              _23 (menu3 -> parentMenu)
        */
    }

    public function testMenu1()
    {
        $menu = $this->_root->getComponentById(2);
        $this->assertEquals('Vps_Component_Cache_Menu_Root3_Menu1_Component', $menu->componentClass);
        $menu = $this->_root->getComponentById('2-subMenu');
        $this->assertEquals('Vps_Component_Cache_Menu_Root3_Menu1_Sub_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById(3);
        $this->assertEquals('Vpc_Basic_ParentContent_Component', $menu->componentClass);
        $this->assertNull($this->_root->getComponentById('3-subMenu'));
    }

    public function testMenu2()
    {
        $menu = $this->_root->getComponentById(12);
        $this->assertEquals('Vps_Component_Cache_Menu_Root3_Menu2_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById(13);
        $this->assertEquals('Vpc_Basic_ParentContent_Component', $menu->componentClass);
    }

    public function testMenu3()
    {
        $menu = $this->_root->getComponentById(22);
        $this->assertEquals('Vps_Component_Cache_Menu_Root3_Menu3_Component', $menu->componentClass);

        $menu = $this->_root->getComponentById(23);
        $this->assertEquals('Vpc_Basic_ParentContent_Component', $menu->componentClass);
    }
}
