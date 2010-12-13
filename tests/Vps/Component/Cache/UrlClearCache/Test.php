<?php
/**
 * @group 
 */
class Vps_Component_Cache_UrlClearCache_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_UrlClearCache_Root');
        $this->_root->setFilename(false);
    }

    public function testCached()
    {
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();
        $d = Vps_Registry::get('testDomain');
        $c = $this->_root->getPageByUrl("http://$d/foo", null); //uncached
        $this->assertEquals('1', $c->componentId);
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('componentDatas'));
        $this->assertEquals(0, (int)Vps_Benchmark::getCounterValue('unserialized componentDatas'));

        Vps_Component_Data_Root::reset();
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Cache_UrlClearCache_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();

        Vps_Benchmark::reset();
        $c = $this->_root->getPageByUrl("http://$d/foo", null); //cached
        $this->assertEquals('1', $c->componentId);
        $this->assertEquals(0, (int)Vps_Benchmark::getCounterValue('componentDatas'));
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('unserialized componentDatas'));
    }

    public function testClearCache1()
    {
        $d = Vps_Registry::get('testDomain');
        $c = $this->_root->getPageByUrl("http://$d/foo", null); //uncached
        $this->assertEquals('1', $c->componentId);

        $m = Vps_Model_Abstract::getInstance('Vps_Component_Cache_UrlClearCache_PageTestModel');
        $r = $m->getRow(1);
        $r->filename = 'fxx';
        $r->save();
        $this->_process();
        Vps_Component_Data_Root::reset();
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Cache_UrlClearCache_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
        $this->_root->setFilename(false);

        $c = $this->_root->getPageByUrl("http://$d/fxx", null); //uncached
        $this->assertEquals('1', $c->componentId);

        $c = $this->_root->getPageByUrl("http://$d/foo", null); //uncached
        $this->assertFalse($c);
    }


    public function testClearCache2()
    {
        $d = Vps_Registry::get('testDomain');
        $c = $this->_root->getPageByUrl("http://$d/foo/bar", null); //uncached
        $this->assertEquals('2', $c->componentId);

        $m = Vps_Model_Abstract::getInstance('Vps_Component_Cache_UrlClearCache_PageTestModel');
        $r = $m->getRow(1);
        $r->filename = 'fxx';
        $r->save();
        $this->_process();
        Vps_Component_Data_Root::reset();
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Cache_UrlClearCache_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
        $this->_root->setFilename(false);

        $c = $this->_root->getPageByUrl("http://$d/fxx/bar", null); //uncached
        $this->assertEquals('2', $c->componentId);
        
        $this->markTestIncomplete();

        $c = $this->_root->getPageByUrl("http://$d/foo/bar", null); //uncached
        $this->assertFalse($c);
    }
}