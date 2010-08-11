<?php
/**
 * @group Component_CacheVars
 */
class Vps_Component_CacheVars_Link_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        $this->markTestIncomplete();
        Vps_Component_Data_Root::setComponentClass('Vps_Component_CacheVars_Link_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testLink()
    {
        $cacheVars = $this->_root
            ->getChildComponent('_link')
            ->getChildComponent('-link')
            ->getComponent()->getCacheVars();
        $cacheVars = array_values($cacheVars);
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Link_InternModel', get_class($cacheVars[0]['model']));
        $this->assertEquals('root_link-link', $cacheVars[0]['id']);

        $cacheVars = $this->_root
            ->getChildComponent('_link')
            ->getComponent()->getCacheVars();
        $cacheVars = array_values($cacheVars);
        $this->assertEquals(3, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Link_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('root_link', $cacheVars[0]['id']);
        $this->assertEquals('Vps_Component_CacheVars_Link_Model', get_class($cacheVars[1]['model']));
        $this->assertEquals('root_link', $cacheVars[1]['id']);
        $this->assertTrue($cacheVars[1]['callback']);
        $this->assertEquals('Vps_Component_CacheVars_Link_Model', get_class($cacheVars[2]['model']));
        $this->assertEquals('root_link', $cacheVars[2]['id']);
        $this->assertEquals('root_link-link', $cacheVars[2]['componentId']);
    }
}
