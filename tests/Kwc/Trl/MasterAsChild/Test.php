<?php
class Kwc_Trl_MasterAsChild_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwc_Trl_MasterAsChild_Root');
    }

    public function testMenu()
    {
        $page = $this->_root->getComponentById('root-en_page');
        $masterAsChild = $page->getChildComponent('-child');
        $subpage = $masterAsChild->getChildComponent('_subpage');
        $box = $subpage->getChildComponent('-box');

        $this->assertEquals('root-master_page', $page->chained->componentId);
        // masterAsChild doesn't have chained
        $this->assertFalse(isset($masterAsChild->chained));
        // child of masterAsChild neither
        $this->assertFalse(isset($subpage->chained));
        // boxes under masterAsChild do have chained (from page of first chained in the hierarchy going up)
        $this->assertEquals('root-master_page-box', $box->chained->componentId);
    }
}
