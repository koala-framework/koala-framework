<?php
class Kwf_Component_Cache_CrossPageClearCacheChild_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_CrossPageClearCacheChild_Root');
    }

    public function testPage1()
    {
        $html = $this->_root->getComponentById('root_page1')->render(true, true);
        $this->assertRegExp("#master.*page1.*foo#s", $html);

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_CrossPageClearCacheChild_IncludedPage_Child_Model')->getRow('root_includedPage-child');
        $row->foo = 'xxx';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('root_page1')->render(true, true);
        $this->assertRegExp("#master.*page1.*xxx#s", $html);
    }

    public function testPage2()
    {
        $html = $this->_root->getComponentById('root_page2')->render(true, true);
        $this->assertRegExp("#master.*page2-child.*foo#s", $html);

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_CrossPageClearCacheChild_IncludedPage_Child_Model')->getRow('root_includedPage-child');
        $row->foo = 'xxx';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('root_page2')->render(true, true);
        $this->assertRegExp("#master.*page2-child.*xxx#s", $html);
    }

    public function testIncludedPage()
    {
        $html = $this->_root->getComponentById('root_includedPage')->render(true, true);
        $this->assertRegExp("#master.*foo#s", $html);

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_CrossPageClearCacheChild_IncludedPage_Child_Model')->getRow('root_includedPage-child');
        $row->foo = 'xxx';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('root_includedPage')->render(true, true);
        $this->assertRegExp("#master.*xxx#s", $html);
    }
}
