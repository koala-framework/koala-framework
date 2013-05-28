<?php
/**
 * @group Cache_Url
 */
class Kwf_Component_Cache_UrlClearCache_Test extends Kwc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Kwf_Component_Cache_UrlClearCache_Root');
        $this->_root->setFilename(false);
    }

    public function testCached()
    {
        Kwf_Benchmark::enable();
        Kwf_Benchmark::reset();
        $d = Kwf_Registry::get('testDomain');

        $c = $this->_root->getPageByUrl("http://$d/foo", null); //uncached
        $this->assertEquals('1', $c->componentId);
        $this->assertEquals(1, Kwf_Benchmark::getCounterValue('componentDatas'));
        $this->assertEquals(0, (int)Kwf_Benchmark::getCounterValue('unserialized componentDatas'));

        Kwf_Component_Data_Root::reset();
        Kwf_Component_Data_Root::setComponentClass('Kwf_Component_Cache_UrlClearCache_Root');
        $this->_root = Kwf_Component_Data_Root::getInstance();

        Kwf_Benchmark::reset();
        $c = $this->_root->getPageByUrl("http://$d/foo", null); //cached
        $this->assertEquals('1', $c->componentId);
        $this->assertEquals(0, (int)Kwf_Benchmark::getCounterValue('componentDatas'));
        $this->assertEquals(1, Kwf_Benchmark::getCounterValue('unserialized componentDatas'));
    }

    public function testClearCache1()
    {
        $d = Kwf_Registry::get('testDomain');
        $c = $this->_root->getPageByUrl("http://$d/foo", null); //uncached
        $this->assertEquals('1', $c->componentId);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_UrlClearCache_PageTestModel');
        $r = $m->getRow(1);
        $r->filename = 'fxx';
        $r->save();

        $this->_process();

        $c = $this->_root->getPageByUrl("http://$d/fxx", null); //uncached
        $this->assertEquals('1', $c->componentId);

        $c = $this->_root->getPageByUrl("http://$d/foo", null); //uncached
        $this->assertFalse(!!$c);
    }


    public function testClearCache2()
    {
        $d = Kwf_Registry::get('testDomain');
        $c = $this->_root->getPageByUrl("http://$d/foo/bar", null); //uncached
        $this->assertEquals('2', $c->componentId);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_UrlClearCache_PageTestModel');
        $r = $m->getRow(1);
        $r->filename = 'fxx';
        $r->save();

        $this->_process();

        $c = $this->_root->getPageByUrl("http://$d/fxx/bar", null); //uncached
        $this->assertEquals('2', $c->componentId);

        $c = $this->_root->getPageByUrl("http://$d/foo/bar", null); //uncached
        $this->assertFalse(!!$c);
    }

    public function testChangeParent()
    {
        $d = Kwf_Registry::get('testDomain');
        $c = $this->_root->getPageByUrl("http://$d/baz", null); //uncached
        $this->assertEquals('3', $c->componentId);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_UrlClearCache_PageTestModel');
        $r = $m->getRow(3);
        $r->parent_id = 1;
        $r->save();

        $this->_process();

        $c = $this->_root->getPageByUrl("http://$d/foo/baz", null); //uncached
        $this->assertEquals('3', $c->componentId);

        $c = $this->_root->getPageByUrl("http://$d/baz", null); //uncached
        $this->assertFalse(!!$c);
    }

    public function testChangeGrandParent()
    {
        $d = Kwf_Registry::get('testDomain');
        $c = $this->_root->getPageByUrl("http://$d/baz/bam", null); //uncached
        $this->assertEquals('4', $c->componentId);

        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_Cache_UrlClearCache_PageTestModel');
        $r = $m->getRow(3);
        $r->parent_id = 1;
        $r->save();

        $this->_process();

        $c = $this->_root->getPageByUrl("http://$d/foo/baz/bam", null); //uncached
        $this->assertEquals('4', $c->componentId);

        $c = $this->_root->getPageByUrl("http://$d/baz/bam", null); //uncached
        $this->assertFalse(!!$c);
    }
}