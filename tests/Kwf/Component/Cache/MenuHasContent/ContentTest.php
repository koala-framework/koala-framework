<?php
/**
 * @group Kwc_Menu
 */
class Kwf_Component_Cache_MenuHasContent_ContentTest extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_MenuHasContent_Root');
        /*
        root
          -menuMain (empty)
          -menuTop (empty)
          -top
            -menuMain (otherCategory)
            -menuTop (menu)
            2 (invisible)
              -menuMain (parentMenu)
              -menuTop (parentMenu)
          -main
            -menuMain (menu)
            -menuTop (otherCategory)
            1
              -menuMain (parentMenu)
              -menuTop (parentMenu)
              3
                -menuMain (parentContent)
                -menuTop (parentContent)
                4
                  -menuMain (parentContent)
                  -menuTop (parentContent)
         */
    }


    public function testInitial()
    {
        $this->assertTrue($this->_root->getComponentById('root-main-menuMain')->hasContent());
        $this->assertFalse($this->_root->getComponentById('root-main-menuTop')->hasContent());

        $this->assertTrue($this->_root->getComponentById('1-menuMain')->hasContent());
        $this->assertFalse($this->_root->getComponentById('1-menuTop')->hasContent());

        $html = $this->_root->getComponentById('1')->render(true, true);
        $this->assertNotContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);

        $html = $this->_root->getComponentById('3')->render(true, true);
        $this->assertNotContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);

        $html = $this->_root->getComponentById('4')->render(true, true);
        $this->assertNotContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);
    }

    public function testAddPageToTop()
    {
        $this->_root->getComponentById('1')->render(true, true);
        $this->_root->getComponentById('3')->render(true, true);
        $this->_root->getComponentById('4')->render(true, true);

        Kwf_Component_Data_Root::reset();

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuHasContent_Category_PagesModel');
        $row = $m->createRow(array(
            'pos'=>3, 'visible'=>true, 'name'=>'f5', 'filename' => 'f5',
                  'parent_id'=>'root-top', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null
        ));
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('1')->render(true, true);
        $this->assertContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);

        $html = $this->_root->getComponentById('3')->render(true, true);
        $this->assertContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);

        $html = $this->_root->getComponentById('4')->render(true, true);
        $this->assertContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);
    }

    public function testAddPageToMain()
    {
        $this->_root->getComponentById('1')->render(true, true);
        $this->_root->getComponentById('3')->render(true, true);
        $this->_root->getComponentById('4')->render(true, true);

        Kwf_Component_Data_Root::reset();

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuHasContent_Category_PagesModel');
        $row = $m->createRow(array(
            'pos'=>3, 'visible'=>true, 'name'=>'f5', 'filename' => 'f5',
                  'parent_id'=>'root-main', 'component'=>'empty', 'is_home'=>false, 'hide'=>false, 'custom_filename' => null
        ));
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('1')->render(true, true);
        $this->assertNotContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);

        $html = $this->_root->getComponentById('3')->render(true, true);
        $this->assertNotContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);

        $html = $this->_root->getComponentById('4')->render(true, true);
        $this->assertNotContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);

    }

    public function testMakePageVisibleFromTop()
    {
        $this->_root->getComponentById('1')->render(true, true);
        $this->_root->getComponentById('3')->render(true, true);
        $this->_root->getComponentById('4')->render(true, true);

        Kwf_Component_Data_Root::reset();
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_MenuHasContent_Category_PagesModel');
        $row = $m->getRow(2);
        $row->visible = true;
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('1')->render(true, true);
        $this->assertContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);

        $html = $this->_root->getComponentById('3')->render(true, true);
        $this->assertContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);

        $html = $this->_root->getComponentById('4')->render(true, true);
        $this->assertContains('menuTopHasContent', $html);
        $this->assertContains('menuMainHasContent', $html);

    }
}