<?php
/**
 * @group Vpc_Trl
 * @group Vpc_Trl_Menu
 */
/*
Ansicht frontend (en wird getestet):
/vps/vpctest/Vpc_Trl_Menu_Root_Component/de
/vps/vpctest/Vpc_Trl_Menu_Root_Component/en
 */
class Vpc_Trl_Menu_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vpc_Trl_Menu_Root_Component');
    }

    public function testMenuForPage1()
    {
        // Menu
        $c = $this->_root->getComponentById('root-en-main_1-menu');
        $vars = $c->getComponent()->getTemplateVars();
        $m = $vars['menu'];
        $this->assertEquals(2, count($m));
        $this->assertEquals('root-en-main_1', $m[0]['data']->componentId);
        $this->assertEquals('Page 1', $m[0]['data']->name);
        $this->assertEquals('p1', $m[0]['data']->filename);
        $this->assertEquals('root-en-main_2', $m[1]['data']->componentId);
        $m = $vars['subMenu'];
        $this->assertEquals('root-en-main_1-menu-subMenu', $m->componentId);

        // Menu-SubMenu
        $vars = $m->getComponent()->getTemplateVars();
        $m = $vars['menu'];
        $this->assertEquals(1, count($m));
        $this->assertEquals('root-en-main_3', $m[0]['data']->componentId);
        $this->assertNull($vars['subMenu']);

        // ExpandedMenu
        $c = $this->_root->getComponentById('root-en-main_1-levelmenu');
        $vars = $c->getComponent()->getTemplateVars();
        $m = $vars['menu'];
        $this->assertEquals(1, count($m));
        $this->assertEquals('root-en-main_3', $m[0]['data']->componentId);
        $m = $m[0]['submenu'];
        $this->assertEquals(1, count($m));
        $this->assertEquals('root-en-main_4', $m[0]['data']->componentId);
        $this->assertFalse(isset($m[0]->submenu));
    }

    public function testMenuForPage2()
    {
        // Menu
        $c = $this->_root->getComponentById('root-en-main_2-menu');
        $vars = $c->getComponent()->getTemplateVars();
        $m = $vars['menu'];
        $this->assertEquals(2, count($m));
        $this->assertEquals('root-en-main_1', $m[0]['data']->componentId);
        $this->assertEquals('root-en-main_2', $m[1]['data']->componentId);
        $m = $vars['subMenu'];
        $this->assertEquals('root-en-main_2-menu-subMenu', $m->componentId);

        // Menu-SubMenu
        $vars = $m->getComponent()->getTemplateVars();
        $m = $vars['menu'];
        $this->assertEquals(0, count($m));
        $this->assertNull($vars['subMenu']);

        // ExpandedMenu
        $c = $this->_root->getComponentById('root-en-main_2-levelmenu');
        $vars = $c->getComponent()->getTemplateVars();
        $this->assertEquals(array(), $vars['menu']);
    }
}
