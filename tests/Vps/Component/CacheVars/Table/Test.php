<?php
/**
 * @group Component_CacheVars
 */
class Vps_Component_CacheVars_Table_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_CacheVars_Table_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testTable()
    {
        $cacheVars = $this->_root->getComponent()->getCacheVars();
        $this->assertEquals(2, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Table_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('1', $cacheVars[0]['id']);
        $this->assertEquals('Vps_Component_CacheVars_Table_Model', get_class($cacheVars[1]['model']));
        $this->assertEquals('2', $cacheVars[1]['id']);
    }

    public function testEmpty()
    {
        $cacheVars = $this->_root
            ->getChildComponent('-1')
            ->getComponent()->getCacheVars();
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Table_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('1', $cacheVars[0]['id']);
    }
}
