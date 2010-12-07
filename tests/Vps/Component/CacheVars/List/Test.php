<?php
/**
 * @group Component_CacheVars
 */
class Vps_Component_CacheVars_List_Test extends Vpc_TestAbstract
{
    public function setUp()
    {
        parent::setUp('Vps_Component_CacheVars_List_Root');
    }

    public function testList()
    {
        $view = $this->_root->getChildComponent('-list');
        $cacheVars = $view->getComponent()->getCacheVars();
        $this->assertEquals(array(), $cacheVars); // Leer, da Komponente eine View hat
    }

    public function testView()
    {
        $view = $this->_root->getChildComponent('-list')
            ->getChildComponent('-view');
        $cacheVars = $view->getComponent()->getCacheVars();
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_List_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('root-list', $cacheVars[0]['id']);
    }

    public function testPartial()
    {
        $view = $this->_root->getChildComponent('-list')
            ->getChildComponent('-view');
        $cacheVars = $view->getComponent()->getPartialCacheVars(1);
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_List_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('root-list', $cacheVars[0]['id']);
    }

    public function testPaging()
    {
        $view = $this->_root->getChildComponent('-list')
            ->getChildComponent('-view')
            ->getChildComponent('-paging');
        $cacheVars = $view->getComponent()->getCacheVars();
        $this->assertEquals(1, count($cacheVars));
        $this->assertEquals('Vps_Component_CacheVars_List_Model', get_class($cacheVars[0]['model']));
        $this->assertEquals('root-list', $cacheVars[0]['id']);
        $partialVars = $view->getComponent()->getPartialCacheVars(1);
        $this->assertEquals($cacheVars, $partialVars);
    }
}
