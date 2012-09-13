<?php
class Kwc_Directories_CategoryTest_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Directories_CategoryTest_Root');
    }

    public function testAddCategory()
    {
        $cat = $this->_root->getComponentById('root_directory-categories_1');
        $html = $cat->render(null, false);
        $this->assertContains('foo1', $html);

        $m = Kwf_Model_Abstract::getInstance('Kwc_Directories_CategoryTest_Category_Directory_ItemsToCategoriesModel');
        $row = $m->createRow();
        $row->item_id = 2;
        $row->category_id = 1;
        $row->save();

        $this->_process();

        $cat = $this->_root->getComponentById('root_directory-categories_1');
        $html = $cat->render(null, false);
        $this->assertContains('foo1', $html);
        $this->markTestIncomplete();
        $this->assertContains('foo2', $html);
    }

    public function testRemoveCategory()
    {
        $cat = $this->_root->getComponentById('root_directory-categories_1');
        $html = $cat->render(null, false);
        $this->assertContains('foo1', $html);

        $m = Kwf_Model_Abstract::getInstance('Kwc_Directories_CategoryTest_Category_Directory_ItemsToCategoriesModel');
        $m->getRow(1)->delete();

        $this->_process();

        $cat = $this->_root->getComponentById('root_directory-categories_1');
        $html = $cat->render(null, false);
        $this->assertNotContains('foo2', $html);
        $this->markTestIncomplete();
        $this->assertNotContains('foo1', $html);
    }

    public function testAddFirstCategory()
    {
        $cat = $this->_root->getComponentById('root_directory-categories_2');
        $html = $cat->render(null, false);
        $this->assertContains('foo2', $html);

        $m = Kwf_Model_Abstract::getInstance('Kwc_Directories_CategoryTest_Category_Directory_ItemsToCategoriesModel');
        $row = $m->createRow();
        $row->item_id = 1;
        $row->category_id = 2;
        $row->save();

        $this->_process();

        $cat = $this->_root->getComponentById('root_directory-categories_2');
        $html = $cat->render(null, false);
        $this->assertContains('foo2', $html);
        $this->markTestIncomplete();
        $this->assertContains('foo1', $html);
    }
}
