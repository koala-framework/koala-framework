<?php
class Kwf_Component_Cache_CrossPageClearCache_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_CrossPageClearCache_Root');
    }

    public function testIt()
    {
        $html = $this->_root->getComponentById('root_page1')->render(true, true);
        $this->assertRegExp("#master.*page1.*page2#s", $html);

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_CrossPageClearCache_Page2_Model')->getRow('root_page2');
        $row->foo = 'xxx';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('root_page1')->render(true, true);
        $this->assertRegExp("#master.*page1.*xxx#s", $html);
    }
}
