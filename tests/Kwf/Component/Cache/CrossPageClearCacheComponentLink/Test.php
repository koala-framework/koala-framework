<?php
class Kwf_Component_Cache_CrossPageClearCacheComponentLink_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_CrossPageClearCacheComponentLink_Root');
    }

    public function testPage1()
    {
        $html = $this->_root->getComponentById('root_page1')->render(true, true);
        $this->assertRegExp("#master.*page1.*test1#s", $html);

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_CrossPageClearCacheComponentLink_PagesModel')->getRow('1');
        $row->name = 'xxx';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('root_page1')->render(true, true);
        $this->assertRegExp("#master.*page1.*xxx#s", $html);
    }

    public function testPage2()
    {
        $html = $this->_root->getComponentById('root_page2')->render(true, true);
        $this->assertRegExp("#master.*page2-child.*test1#s", $html);

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_CrossPageClearCacheComponentLink_PagesModel')->getRow('1');
        $row->name = 'xxx';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('root_page2')->render(true, true);
        $this->assertRegExp("#master.*page2-child.*xxx#s", $html);
    }
}
