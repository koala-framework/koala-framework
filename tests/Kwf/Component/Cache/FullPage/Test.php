<?php
class Kwf_Component_Cache_FullPage_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_FullPage_Root');
    }

    private function _getViewCacheCount()
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('deleted', false);
        return Kwf_Component_Cache::getInstance()->getModel()->countRows($s);
    }

    public function testEmbedContent()
    {
        $html = $this->_root->getChildComponent('_test1')->render(null, true);
        $this->assertContains('content2', $html);
        $this->assertContains('/test3"', $html);

        $this->assertEquals(7, $this->_getViewCacheCount());
        /*
        - root_test1:page
        - root_test1:master
        - root_test1:component
        - root_test2:component       [delete]
        - root_test4:componentLink
        - root_test3:componentLink
        - root_test1:fullPage        [delete]
        */

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_FullPage_Test2_Model')->getRow('root_test2');
        $row->test = 'foo2';
        $row->save();
        $this->_process();

        $this->assertEquals(5, $this->_getViewCacheCount());
        $html = $this->_root->getChildComponent('_test1')->render(null, true);
        $this->assertContains('foo2', $html);
        $this->assertContains('/test3"', $html);
        $this->assertEquals(7, $this->_getViewCacheCount());
    }

    public function testLinkTargetContentsChanged()
    {
        $html = $this->_root->getChildComponent('_test1')->render(null, true);
        $this->_root->getChildComponent('_test3')->render(null, true);
        $this->assertContains('content2', $html);
        $this->assertContains('/test3"', $html);
        $this->assertEquals(7+4, $this->_getViewCacheCount());

        $row = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_FullPage_Test3_Model')->getRow('root_test3');
        $row->test = 'foo3';
        $row->save();
        $this->_process();
        /*
        - root_test1:page
        - root_test1:master
        - root_test1:component
        - root_test2:component
        - root_test4:componentLink
        - root_test3:componentLink
        - root_test1:fullPage

        - root_test3:page
        - root_test3:master
        - root_test3:component       [delete]
        - root_test3:fullPage        [delete]
        */

        $this->markTestIncomplete();
        $this->assertEquals(7+4-2, $this->_getViewCacheCount());
        $html = $this->_root->getChildComponent('_test1')->render(null, true);
        $this->assertContains('content2', $html);
        $this->assertContains('/test3"', $html);
        $this->assertEquals(7+4-2, $this->_getViewCacheCount());
    }

    public function testLinkTargetChanged1()
    {
        $html = $this->_root->getChildComponent('_test1')->render(null, true);
        $this->assertContains('content2', $html);
        $this->assertContains('/test3"', $html);
        $this->assertEquals(7, $this->_getViewCacheCount());
        /*
        - root_test1:page
        - root_test1:master
        - root_test1:component
        - root_test2:component
        - root_test4:componentLink
        - root_test3:componentLink   [delete]
        - root_test1:fullPage        [delete]
        */
        Kwf_Component_Events::fireEvent(new Kwf_Component_Event_Page_UrlChanged('Kwf_Component_Cache_FullPage_Test3_Component', $this->_root->getChildComponent('_test3')));
        $this->_process();

        $this->assertEquals(5, $this->_getViewCacheCount());
        $html = $this->_root->getChildComponent('_test1')->render(null, true);
        $this->assertContains('content2', $html);
        $this->assertContains('/test3"', $html);
        $this->assertEquals(7, $this->_getViewCacheCount());
    }

    public function testLinkTargetChanged2()
    {
        $html = $this->_root->getChildComponent('_test1')->render(null, true);
        $this->assertContains('content2', $html);
        $this->assertContains('/test3"', $html);
        $this->assertEquals(7, $this->_getViewCacheCount());
        /*
        - root_test1:page
        - root_test1:master
        - root_test1:component
        - root_test2:component
        - root_test4:componentLink   [delete]
        - root_test3:componentLink
        - root_test1:fullPage        [deleted]
        */


        $this->_root->getChildComponent('_test2')->render(null, true);
        /*
        - root_test2:page
        - root_test2:master
        - root_test2:fullPage        [delete]
        */
        $this->assertEquals(7+3, $this->_getViewCacheCount());


        $this->_root->getChildComponent('_test3')->render(null, true);
        /*
        - root_test3:page
        - root_test3:master
        - root_test3:component
        - root_test3:fullPage
        */
        $this->assertEquals(7+3+4, $this->_getViewCacheCount());


        $this->_root->getChildComponent('_test4')->render(null, true);
        /*
        - root_test4:page
        - root_test4:master
        - root_test4:component
        - root_test4:fullPage        [delete]
        */
        $this->assertEquals(7+3+4+4, $this->_getViewCacheCount());

        Kwf_Component_Events::fireEvent(new Kwf_Component_Event_Page_UrlChanged('Kwf_Component_Cache_FullPage_Test4_Component', $this->_root->getChildComponent('_test4')));
        $this->_process();

        $this->assertEquals(7+3+4+4 - 4, $this->_getViewCacheCount());

        $html = $this->_root->getChildComponent('_test1')->render(null, true);
        $this->assertContains('content2', $html);
        $this->assertContains('/test3"', $html);
        $this->assertEquals(7+3+4+4 - 4 +2, $this->_getViewCacheCount());

        $this->_root->getChildComponent('_test2')->render(null, true);
        $this->assertEquals(7+3+4+4 - 4 +2+1, $this->_getViewCacheCount());

        $this->_root->getChildComponent('_test3')->render(null, true);
        $this->assertEquals(7+3+4+4 - 4 +2+1, $this->_getViewCacheCount());

        $this->_root->getChildComponent('_test4')->render(null, true);
        $this->assertEquals(7+3+4+4 - 4 +2+1+1, $this->_getViewCacheCount());
    }
}
