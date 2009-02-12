<?php
/**
 * @group Component_CacheVars
 */
class Vps_Component_CacheVars_Link_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_CacheVars_Link_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testLink()
    {
        $cacheVars = $this->_root
            ->getChildComponent('_link')
            ->getComponent()->getCacheVars();
        $this->assertEquals(0, count($cacheVars));

        $cacheVars = $this->_root
            ->getChildComponent('_link')
            ->getChildComponent('-link')
            ->getComponent()->getCacheVars();
        $this->assertEquals(2, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Link_InternModel', $cacheVars[0]['model']);
        $this->assertEquals('root_link-link', $cacheVars[0]['id']);
        $this->assertEquals('Vps_Component_CacheVars_Link_Model', $cacheVars[1]['model']);
        $this->assertEquals('root_link', $cacheVars[1]['id']);
    }
}
