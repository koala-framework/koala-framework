<?php
/**
 * @group Component_CacheVars
 */
class Vps_Component_CacheVars_Box_Test extends PHPUnit_Framework_TestCase
{
    private $_root;

    public function setUp()
    {
        Vps_Component_Data_Root::setComponentClass('Vps_Component_CacheVars_Box_Root');
        $this->_root = Vps_Component_Data_Root::getInstance();
    }

    public function testBox()
    {
        $cacheVars = $this->_root
            ->getComponent()->getCacheVars();
        $this->assertEquals(array(), $cacheVars);

        $cacheVars = $this->_root
            ->getChildComponent('-box')
            ->getComponent()->getCacheVars();
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Box_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('root-box', $cacheVars[0]['id']);

        $cacheVars = $this->_root
            ->getChildComponent('_boxNotOverwritten')
            ->getChildComponent('-box')
            ->getComponent()->getCacheVars();
           // p($cacheVars);
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Box_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('root_boxNotOverwritten-box', $cacheVars[0]['id']);

        $cacheVars = $this->_root
            ->getChildComponent('_boxOverwritten')
            ->getChildComponent('-box')
            ->getComponent()->getCacheVars();
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Box_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('root_boxOverwritten-box', $cacheVars[0]['id']);
    }

    public function testBoxUnique()
    {
        $cacheVars = $this->_root
            ->getComponent()->getCacheVars();
        $this->assertEquals(array(), $cacheVars);

        $cacheVars = $this->_root
            ->getChildComponent('-boxUnique')
            ->getComponent()->getCacheVars();
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Box_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('root-boxUnique', $cacheVars[0]['id']);

        $cacheVars = $this->_root
            ->getChildComponent('_boxNotOverwritten')
            ->getChildComponent('-boxUnique')
            ->getComponent()->getCacheVars();
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Box_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('root-boxUnique', $cacheVars[0]['id']);

        $cacheVars = $this->_root
            ->getChildComponent('_boxOverwritten')
            ->getChildComponent('-boxUnique')
            ->getComponent()->getCacheVars();
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_Box_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('root-boxUnique', $cacheVars[0]['id']);
    }
}
