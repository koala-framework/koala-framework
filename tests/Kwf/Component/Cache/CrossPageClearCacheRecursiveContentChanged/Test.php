<?php
class Kwf_Component_Cache_CrossPageClearCacheRecursiveContentChanged_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_CrossPageClearCacheRecursiveContentChanged_Root');
    }

    public function testInitial()
    {
        $html = $this->_root->getComponentById('1')->render(true, true);
        $this->assertRegExp("#master.*1-box#s", $html);

        $html = $this->_root->getComponentById('2')->render(true, true);
        $this->assertRegExp("#master.*1-box#s", $html);

        $html = $this->_root->getComponentById('3')->render(true, true);
        $this->assertRegExp("#master.*root-box#s", $html);
    }

    public function testChangeParent()
    {
        $html = $this->_root->getComponentById('2')->render(true, true);
        $this->assertRegExp("#master.*1-box#s", $html);

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_CrossPageClearCacheRecursiveContentChanged_PagesModel')->getRow('2');
        $row->parent_id = 'root';
        $row->save();
        $this->_process();

        $html = $this->_root->getComponentById('2')->render(true, true);
        $this->assertRegExp("#master.*root-box#s", $html);
    }

}
