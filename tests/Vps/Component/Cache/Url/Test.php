<?php
/**
 * @group 
 */
class Vps_Component_Cache_Url_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_Cache_Url_Root');
        $this->_root->setFilename(false);
    }

    public function testIt()
    {
        Vps_Benchmark::enable();
        Vps_Benchmark::reset();
        $d = Vps_Registry::get('testDomain');
        $c = $this->_root->getPageByUrl("http://$d/page1", null); //uncached
        $this->assertEquals('root_page1', $c->componentId);
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('componentDatas'));
        $this->assertEquals(0, (int)Vps_Benchmark::getCounterValue('unserialized componentDatas'));

        Vps_Component_Data_Root::reset();
        Vps_Component_Data_Root::setComponentClass('Vps_Component_Cache_Url_Root');
        $root = Vps_Component_Data_Root::getInstance();

        Vps_Benchmark::reset();
        $c = $root->getPageByUrl("http://$d/page1", null); //cached
        $this->assertEquals('root_page1', $c->componentId);
        $this->assertEquals(0, (int)Vps_Benchmark::getCounterValue('componentDatas'));
        $this->assertEquals(1, Vps_Benchmark::getCounterValue('unserialized componentDatas'));
    }
}
