<?php
class Kwc_Trl_FirstChildPage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_FirstChildPage_Root');
        $this->_root->setFilename('');
    }

    /*
    root
      master
        cat1
          1 (firstChildPage)
            2 (empty)
            3 (empty)
          4 (firstChildPage)
          5 (firstChildPage)
            6 (firstChildPage)
              7 (empty)
    */
    public function testDe()
    {
        $c = $this->_root->getComponentById('root-master_testLink');
        $html = $c->render();
        $this->assertRegExp('#<div class=\"link1\"><a .*?href="/de/foo1/foo2".*?></div>#', $html);
        $this->assertRegExp('#<div class=\"link4\"></div>#', $html);
        $this->assertRegExp('#<div class=\"link5\"><a .*?href="/de/foo5/foo6/foo7".*?></div>#', $html);
    }

    public function testEn()
    {
        $c = $this->_root->getComponentById('root-en_testLink');
        $html = $c->render();
        $this->assertRegExp('#<div class=\"link1\"><a .*?href="/en/foo1en/foo2en".*?></div>#', $html);
        $this->assertRegExp('#<div class=\"link5\"><a .*?href="/en/foo5en/foo6en/foo7en".*?></div>#', $html);
        $this->markTestIncomplete();
        $this->assertRegExp('#<div class=\"link4\"></div>#', $html);
    }

    public function testCacheChangePageDe()
    {
        $c = $this->_root->getComponentById('root-master_testLink');
        $c->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_FirstChildPage_Category_PagesModel')->getRow(2);
        $row->visible = false;
        $row->save();
        $this->_process();

        $html = $c->render();
        $this->assertRegExp('#<div class=\"link1\"><a .*?href="/de/foo1/foo3".*?></div>#', $html);
    }

    public function testCacheChangePageEn()
    {
        $c = $this->_root->getComponentById('root-en_testLink');
        $c->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_FirstChildPage_Category_Trl_PagesModel')->getRow('root-en-cat1_2');
        $row->visible = false;
        $row->save();
        $this->_process();
        
        $html = $c->render();
        $this->markTestIncomplete();
        $this->assertRegExp('#<div class=\"link1\"><a .*?href="/en/foo1en/foo3en".*?></div>#', $html);
    }

    public function testCacheHideAllPagesDe()
    {
        $c = $this->_root->getComponentById('root-master_testLink');
        $c->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_FirstChildPage_Category_PagesModel')->getRow(2);
        $row->visible = false;
        $row->save();
        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_FirstChildPage_Category_PagesModel')->getRow(3);
        $row->visible = false;
        $row->save();
        $this->_process();

        $html = $c->render();
        $this->assertRegExp('#<div class=\"link1\"></div>#', $html);
    }

    public function testCacheHideAllPagesEn()
    {
        $c = $this->_root->getComponentById('root-en_testLink');
        $c->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_FirstChildPage_Category_Trl_PagesModel')->getRow('root-en-cat1_2');
        $row->visible = false;
        $row->save();
        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_FirstChildPage_Category_Trl_PagesModel')->getRow('root-en-cat1_3');
        $row->visible = false;
        $row->save();
        $this->_process();

        $html = $c->render();
        $this->markTestIncomplete();
        $this->assertRegExp('#<div class=\"link1\"></div>#', $html);
    }

    public function testCacheChangeRenamePageDe()
    {
        $c = $this->_root->getComponentById('root-master_testLink');
        $c->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_FirstChildPage_Category_PagesModel')->getRow('7');
        $row->filename = 'foo7x';
        $row->save();
        $this->_process();

        $html = $c->render();
        $this->assertRegExp('#<div class=\"link5\"><a .*?href="/de/foo5/foo6/foo7x".*?></div>#', $html);
    }

    public function testCacheChangeRenamePageEn()
    {
        $c = $this->_root->getComponentById('root-en_testLink');
        $c->render();

        $row = Kwf_Model_Abstract::getInstance('Kwc_Trl_FirstChildPage_Category_Trl_PagesModel')->getRow('root-en-cat1_7');
        $row->filename = 'foo7enx';
        $row->save();
        $this->_process();

        $html = $c->render();
        $this->markTestIncomplete();
        $this->assertRegExp('#<div class=\"link5\"><a .*?href="/en/foo5en/foo6en/foo7enx".*?></div>#', $html);
    }
}
