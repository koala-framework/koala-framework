<?php
/**
 * @group Kwc_Menu
 */
class Kwf_Component_Cache_Menu_RecursiveTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_Menu_Root4_Component');
        /*
        root
          -submenu (Kwf_Component_Cache_Menu_Root4_Submenu_Component)
          _1 (/f1)
            -submenu (Kwf_Component_Cache_Menu_Root4_Submenu_Component)
            _2 (/f1/f2)
              -submenu (Kwc_Menu_ParentMenu_Component.Kwf_Component_Cache_Menu_Root4_Submenu_Component)
              _3 (f1/f2/f3)
                -submenu (Kwc_Menu_ParentContent_Component.Kwf_Component_Cache_Menu_Root4_Submenu_Component)
          _4 (/f4)
            -menu-menu
            _2 (/f1/f2)
              -menu
              _3 (f1/f2/f3)
                -parent-menu
        */
    }

    public function testRecursiveRemoved()
    {
        $model = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_Menu_Root4_Page_Model');

        $page = $this->_root->getComponentById('root_page');
        $html = $page->render(true, true);
        $this->assertEquals(1, substr_count($html, '1_1'));

        $row = $model->getRow(1);
        $row->visible = false;
        $row->save();
        $this->_process();

        $page = $this->_root->getComponentById('root_page');
        $html = $page->render(true, true);
        $this->assertEquals('', $html);
    }
}
