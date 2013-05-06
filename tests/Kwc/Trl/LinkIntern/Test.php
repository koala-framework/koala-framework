<?php
class Kwc_Trl_LinkIntern_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_LinkIntern_Root');
        $this->_root->setFilename('');
    }

    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_test1'); //links to visible
        $this->assertRegExp('#<a .*?href="/de/foo1">#', $c->render());

        $c = $this->_root->getComponentById('root-master_test2'); //links to invisible
        $this->assertEquals('', $c->render());
    }

    public function testCacheShowDePage()
    {
        $c = $this->_root->getComponentById('root-master_test2'); //links to invisible
        $c->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_LinkIntern_Category_PagesModel')->getRow(2);
        $row->visible = true;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-master_test2'); //links to now visible
        $this->assertRegExp('#<a .*?href="/de/foo2">#', $c->render());
    }

    public function testCacheHideDePage()
    {
        $c = $this->_root->getComponentById('root-master_test1'); //links to visible
        $c->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_LinkIntern_Category_PagesModel')->getRow(1);
        $row->visible = false;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-master_test1'); //links to now invisible
        $this->assertEquals('', $c->render());
    }

    public function testCacheRenamePage()
    {
        $c = $this->_root->getComponentById('root-master_test1'); //links to visible
        $c->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_LinkIntern_Category_PagesModel')->getRow(1);
        $row->filename = 'foo1x';
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-master_test1'); //links to now invisible
        $this->assertRegExp('#<a .*?href="/de/foo1x">#', $c->render());
    }

    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_test1');
        $this->assertRegExp('#<a .*?href="/en/foo1en">#', $c->render());

        $c = $this->_root->getComponentById('root-en_test2'); //links to invisible
        $this->assertEquals('', $c->render());
    }

    public function testShowEnPage()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_LinkIntern_Category_Trl_PagesModel')->getRow('root-en-cat1_2');
        $row->visible = true;
        $row->save();
        $c = $this->_root->getComponentById('root-en_test2'); //links to invisible
        $this->assertRegExp('#<a .*?href="/en/foo2en">#', $c->render());
    }

    public function testCacheShowEnPage()
    {
        $c = $this->_root->getComponentById('root-en_test2'); //links to invisible
        $c->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_LinkIntern_Category_Trl_PagesModel')->getRow('root-en-cat1_2');
        $row->visible = true;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-en_test2'); //links to invisible
        $this->assertRegExp('#<a .*?href="/en/foo2en">#', $c->render());
    }

    public function testHideEnPage()
    {
        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_LinkIntern_Category_Trl_PagesModel')->getRow('root-en-cat1_1');
        $row->visible = false;
        $row->save();
        $c = $this->_root->getComponentById('root-en_test1'); //links to now invisible
        $this->assertEquals('', $c->render());
    }

    public function testCacheHideEnPage()
    {
        $c = $this->_root->getComponentById('root-en_test1'); //links to visible
        $c->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_LinkIntern_Category_Trl_PagesModel')->getRow('root-en-cat1_1');
        $row->visible = false;
        $row->save();
        $this->_process();

        $c = $this->_root->getComponentById('root-en_test1'); //links to now invisible
        $this->assertEquals('', $c->render());
    }
}
